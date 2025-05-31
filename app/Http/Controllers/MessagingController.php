<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\StudentFile;
use App\Models\TeacherFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessagingController extends Controller
{
    public function compose(Request $request) {
        $user = Auth::user();

        // if user is a student , we return all of his group teachers
        if($user->role === 'student') {
            // if recipient id is provided in the request
            if($request->id) {
                $recipients = collect([User::findOrFail($request->id)]);
                
            }else {
                $groupId = $user->student->group_id;

                $recipients = User::whereHas('teacher.classes', function ($query) use ($groupId) {
                    $query->where('group_id', $groupId);
                })->with('teacher')->get();
            }

        } elseif($user->role == 'teacher') {
            // TO-DO: complete this part
        } else {
            // TO-DO: complete this part
        }

        //dd($recipients);

        return view('compose_message', compact([
            'recipients',
        ]));
    }

    public function store(Request $request) {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,webp',
        ]);

        $user = Auth::user(); // sender
        $recipientId = $request->recipient_id;

        // create a conversation , like in gmail
        $conversation = Conversation::create([
            'subject' => $request->subject,
            'is_group' => false, // one2one style
            'created_by' => $user->id,
        ]);

        // who is in this conversation ?
        $conversation->participants()->attach([$user->id, $recipientId]);

        // create the message
        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $request->body,
        ]);

        // if there is attachments, we handle them here
        if($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('messages', 'public');

                // store in target file table only
                if($user->role === 'student') {
                    StudentFile::create([
                        'student_id' => $user->student->id,
                        'file_path' => $path,
                        'fileable_id' => $message->id,
                        'fileable_type' => Message::class,
                    ]);
                }elseif($user->role === 'teacher') {
                    TeacherFile::create([
                        'teacher_id' => $user->teacher->id,
                        'file_path' => $path,
                        'fileable_id' => $message->id,
                        'fileable_type' => Message::class,
                    ]);
                }
            }
        }

        return back()->with('success', 'Message sent successfully !');
    }

    public function show(Conversation $conversation) {
        $user = Auth::user();

        if (! $conversation->participants->contains($user->id)) {
            abort(403);
        }

        // mark as read
        $conversation->participants()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);

        $conversation->load(['messages.sender', 'messages.studentFiles', 'messages.teacherFiles']);

        return view('conversation', compact('conversation'));
    }

    public function reply(Request $request, Conversation $conversation) {
        $request->validate([
            'body' => 'required|string',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,webp',
        ]);

        $user = Auth::user();
        if (! $conversation->participants->contains(Auth::id())) {
            abort(403);
        }

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'body' => $request->body,
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('messages', 'public');
    
                if ($user->role === 'student') {
                    StudentFile::create([
                        'student_id' => $user->student->id,
                        'file_path' => $path,
                        'fileable_id' => $message->id,
                        'fileable_type' => Message::class,
                    ]);
                } elseif ($user->role === 'teacher') {
                    TeacherFile::create([
                        'teacher_id' => $user->teacher->id,
                        'file_path' => $path,
                        'fileable_id' => $message->id,
                        'fileable_type' => Message::class,
                    ]);
                }
            }
        }

        $conversation->touch();

        return back()->with('success', 'Reply sent.');
    }
}
