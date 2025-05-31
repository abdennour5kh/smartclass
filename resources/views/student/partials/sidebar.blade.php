    <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <h6 style="margin: 0 !important;padding: 0.85rem 2.5rem 0.75rem 1.25rem;font-weight: bold;font-size: 15px;">Overview</h6>
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('student_dashboard') }}">
              <i class="mdi mdi-home menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('student_schedule') }}">
              <i class="mdi mdi-grid-large menu-icon"></i>
              <span class="menu-title">Schedule</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('student_classes') }}">
              <i class="mdi mdi-book-open-page-variant  menu-icon"></i>
              <span class="menu-title">Classes</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('student_justifications') }}">
              <i class="mdi mdi-sync-alert menu-icon"></i>
              <span class="menu-title">Justifications</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('student_documents') }}">
              <i class="mdi mdi-file-document menu-icon"></i>
              <span class="menu-title">Documents</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('student_show_teachers') }}">
              <i class="mdi mdi-account-multiple menu-icon"></i>
              <span class="menu-title">My Teachers</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('student_inbox') }}">
              <i class="mdi mdi-facebook-messenger  menu-icon"></i>
              <span class="menu-title">Inbox</span>
            </a>
          </li>
          

        </ul>
        <h6 style="margin: 0 !important;padding: 0.85rem 2.5rem 0.75rem 1.25rem;font-weight: bold;font-size: 15px;">Mentors</h6>
          @foreach ($currentStudentTeachers as $teacher)
            <a href="{{ route('student_compose_message', $teacher->user->id) }}">
            <div class="mentor-container">
                <div class="mentor-card">
                    <img src="{{ $teacher->img_url ? asset('storage/' . $teacher->img_url) : asset('images/default-avatar.png') }}" alt="Mentor Photo" class="mentor-img">
                    <div class="mentor-info">
                        <div class="mentor-name">{{ $teacher->user->full_name }}</div>
                        <div class="mentor-subject">{{ $teacher->grade }}</div>
                    </div>
                </div>

            </div>
            </a>
          @endforeach

            
        
      </nav>
      <style>
.mentor-card {
  display: flex;
  align-items: center;
  background-color: #f0f0f0;
  border-radius: 40px;
  padding: 10px 15px;
  margin-bottom: 0px;
  width: 100%;
  max-width: 500px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.mentor-img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  margin-right: 15px;
  flex-shrink: 0;
}

.mentor-info {
  flex-grow: 1;
}

.mentor-name {
  font-weight: 600;
  font-size: 0.8rem;
  color: #333;
}

.mentor-subject {
  font-size: 0.675rem;
  color: #777;
}

@media (max-width: 600px) {
  .mentor-card {
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 15px;
    max-width: 200px;
  }

  .mentor-img {
    margin-right: 0;
    margin-bottom: 10px;
  }
}
.mentor-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 10px;
  padding-bottom: 5px;
}

      </style>