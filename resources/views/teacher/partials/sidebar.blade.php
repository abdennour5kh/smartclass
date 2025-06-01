    <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <h6 style="margin: 0 !important;padding: 0.85rem 2.5rem 0.75rem 1.25rem;font-weight: bold;font-size: 15px;">Overview</h6>
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" style="color: #4ebe38 !important;" href="/">
              <i class="mdi mdi-home menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('teacher_announcement') }}">
              <i class="mdi mdi-circle-outline menu-icon"></i>
              <span class="menu-title">Announcements</span>
              
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('teacher_classes_overview') }}">
              <i class="mdi mdi-book-open-page-variant menu-icon"></i>
              <span class="menu-title">My Classes</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('teacher_create_task') }}">
              <i class="mdi mdi-view-headline menu-icon"></i>
              <span class="menu-title">Taskes</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('teacher_inbox') }}">
              <i class="mdi mdi-facebook-messenger menu-icon"></i>
              <span class="menu-title">Inbox</span>
            </a>
          </li>
          

        </ul>
        

            
        
        
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