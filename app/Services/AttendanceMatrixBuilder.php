<?php

namespace App\Services;

use App\Models\Session;
use App\Models\Attendance;
use Illuminate\Support\Collection;

class AttendanceMatrixBuilder 
{
    /**
     * @author Abdennour Khelfi
     * @param array $sessions - Array of Session models (must all belong to the same module!)
     * @param \App\Models\Group $group - The group we are exporting for (e.g. Group 10)
     * @return array [$orderedSessions, $rows]
     */
    public function getAttendanceMatrixForSessions(array $sessions, $group): array
    {
        if (empty($sessions)) return [[], []];

        // Get the module ID of the first session
        $moduleId = $sessions[0]->classe->module_id;
        $expectedGroupId = $sessions[0]->classe->group_id;

        // Validate all sessions belong to the same module & same group
        foreach ($sessions as $s) {
            if ($s->classe->module->id !== $moduleId) {
                throw new \InvalidArgumentException("All sessions must belong to the same module.");
            }

            if ($s->classe->group_id !== $expectedGroupId) {
                throw new \InvalidArgumentException("All sessions must belong to the same group.");
            }
        }

        if ($group->id !== $expectedGroupId) {
            throw new \InvalidArgumentException("Provided group does not match the group of the provided sessions.");
        }

        // Sort sessions by date
        $orderedSessions = collect($sessions)->sortBy('session_date')->values();

        // Get students currently in the group
        $students = $group->students;

        $rows = [];

        foreach ($students as $student) {
            $studentRow = [
                'registration_num' => $student->registration_num,
                'last_name'        => $student->last_name,
                'first_name'       => $student->first_name,
                'attendances'      => []
            ];

            // Get all attendances for this module
            $pastAttendances = Attendance::with('session.classe')
                ->where('student_id', $student->id)
                ->whereHas('session.classe', fn($q) => $q->where('module_id', $moduleId))
                ->get();

            foreach ($orderedSessions as $targetSession) {
                // Student attended the session in the current Group
                $direct = $pastAttendances->firstWhere('session_id', $targetSession->id);

                if ($direct) {
                    $studentRow['attendances'][] = [$direct->status, $direct->notes];
                } else {
                    // Look for attendance in a session of another group
                    $other = $pastAttendances->filter(fn($a) =>
                        $a->session->classe->group_id !== $group->id
                    )->sortBy(fn($a) =>
                        abs(strtotime($a->session->session_date) - strtotime($targetSession->session_date))
                    )->first();

                    if ($other) {
                        $studentRow['attendances'][] = [$other->status, $other->notes];
                    } else {
                        $studentRow['attendances'][] = ['absent', ''];
                    }
                }
            }

            $rows[] = $studentRow;
        }

        return [$orderedSessions, $rows];
    }
}