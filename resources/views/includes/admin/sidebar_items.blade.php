<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
        <i class="nav-icon fa fa-calendar-check-o"></i>
        <p>
            Employees
            <i class="fas fa-angle-left right"></i>
            <span class="badge badge-info right">2</span>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a
                href="{{ route('admin.employees.create') }}"
                class="nav-link"
            >
                <i class="far fa-circle nav-icon"></i>
                <p>Add Employee</p>
            </a>
        </li>
        <li class="nav-item">
            <a
                href="{{ route('admin.employees.index') }}"
                class="nav-link"
            >
                <i class="far fa-circle nav-icon"></i>
                <p>List Employees</p>
            </a>
        </li>
        <li class="nav-item">
            <a
                href="{{ route('admin.employees.mail') }}"
                class="nav-link"
            >
                <i class="far fa-circle nav-icon"></i>
                <p>Miail Employees Details</p>
            </a>
        </li>
    </ul>
</li>