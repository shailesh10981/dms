<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="<?php echo e(route('dashboard')); ?>" class="brand-link text-center">
    <span class="brand-text font-weight-light"> <?php echo e(sys_setting('general.app_name', config('app.name'))); ?></span>
  </a>

  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <i class="fas fa-user-circle img-circle elevation-2" style="font-size: 2rem; color: #fff;"></i>
      </div>
      <div class="info">
        <a href="#" class="d-block"><?php echo e(Auth::user()->name); ?></a>
        <small class="text-muted"><?php echo e(Auth::user()->getRoleNames()->first()); ?></small>
      </div>
    </div>

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['document_upload', 'document_view'])): ?>
        <li class="nav-item <?php echo e(request()->routeIs('documents.*') ? 'menu-open' : ''); ?>">
          <a href="#" class="nav-link <?php echo e(request()->routeIs('documents.*') ? 'active' : ''); ?>">
            <i class="nav-icon fas fa-file"></i>
            <p>
              Documents
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('document_upload')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('documents.create')); ?>" class="nav-link <?php echo e(request()->routeIs('documents.create') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Upload Document</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('document_view')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('documents.index')); ?>" class="nav-link <?php echo e(request()->routeIs('documents.index') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>View Documents</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('document_view')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('documents.trash')); ?>" class="nav-link <?php echo e(request()->routeIs('documents.trash') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Trash</p>
              </a>
            </li>
            <?php endif; ?>
          </ul>
        </li>
        <?php endif; ?>

        <!-- Compliance Module Menu -->
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['template_manage', 'template_view', 'report_create', 'report_view'])): ?>
        <li class="nav-item <?php echo e(request()->routeIs(['compliance.templates.*', 'compliance.reports.*']) ? 'menu-open' : ''); ?>">
          <a href="#" class="nav-link <?php echo e(request()->routeIs(['compliance.templates.*', 'compliance.reports.*']) ? 'active' : ''); ?>">
            <i class="nav-icon fas fa-clipboard-check"></i>
            <p>
              Compliance
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <!-- Templates Section -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['template_manage', 'template_view'])): ?>
            <li class="nav-item <?php echo e(request()->routeIs('compliance.templates.*') ? 'menu-open' : ''); ?>">
              <a href="#" class="nav-link <?php echo e(request()->routeIs('compliance.templates.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-file-alt"></i>
                <p>
                  Compliance Templates
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('template_manage')): ?>
                <li class="nav-item">
                  <a href="<?php echo e(route('compliance.templates.create')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('compliance.templates.create') ? 'active' : ''); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Create Template</p>
                  </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                  <a href="<?php echo e(route('compliance.templates.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('compliance.templates.index') ? 'active' : ''); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>View Templates</p>
                  </a>
                </li>
              </ul>
            </li>
            <?php endif; ?>

            <!-- Reports Section -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['report_create', 'report_view'])): ?>
            <li class="nav-item <?php echo e(request()->routeIs('compliance.reports.*') ? 'menu-open' : ''); ?>">
              <a href="#" class="nav-link <?php echo e(request()->routeIs('compliance.reports.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-clipboard-check"></i>
                <p>
                  Compliance Reports
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('report_create')): ?>
                <li class="nav-item">
                  <a href="<?php echo e(route('compliance.reports.create')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('compliance.reports.create') ? 'active' : ''); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Create Report</p>
                  </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                  <a href="<?php echo e(route('compliance.reports.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('compliance.reports.index') ? 'active' : ''); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>View Reports</p>
                  </a>
                </li>
              </ul>
            </li>
            <?php endif; ?>
          </ul>
        </li>
        <?php endif; ?>




        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_access')): ?>
        <li class="nav-header">ADMINISTRATION</li>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['user_view', 'user_create', 'user_edit', 'user_delete'])): ?>
        <li class="nav-item <?php echo e(request()->routeIs('admin.users.*') ? 'menu-open' : ''); ?>">
          <a href="#" class="nav-link <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>
              User Management
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_create')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('admin.users.create')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.users.create') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Add New User</p>
              </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
              <a href="<?php echo e(route('admin.users.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.users.index') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>View Users</p>
              </a>
            </li>
          </ul>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['role_view', 'role_create', 'role_edit', 'role_delete'])): ?>
        <li class="nav-item">
          <a href="<?php echo e(route('admin.roles.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.roles.*') ? 'active' : ''); ?>">
            <i class="nav-icon fas fa-user-tag"></i>
            <p>Role Management</p>
          </a>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['department_view', 'department_create', 'department_edit', 'department_delete'])): ?>
        <li class="nav-item <?php echo e(request()->routeIs('admin.departments.*') ? 'menu-open' : ''); ?>">
          <a href="#" class="nav-link <?php echo e(request()->routeIs('admin.departments.*') ? 'active' : ''); ?>">
            <i class="nav-icon fas fa-building"></i>
            <p>
              Departments
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('department_create')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('admin.departments.create')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.departments.create') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Add Department</p>
              </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
              <a href="<?php echo e(route('admin.departments.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.departments.index') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>View Departments</p>
              </a>
            </li>
          </ul>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['location_view', 'location_create', 'location_edit', 'location_delete'])): ?>
        <li class="nav-item <?php echo e(request()->routeIs('admin.locations.*') ? 'menu-open' : ''); ?>">
          <a href="#" class="nav-link <?php echo e(request()->routeIs('admin.locations.*') ? 'active' : ''); ?>">
            <i class="nav-icon fas fa-map-marker-alt"></i>
            <p>
              Locations
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('location_create')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('admin.locations.create')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.locations.create') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Add Location</p>
              </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
              <a href="<?php echo e(route('admin.locations.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.locations.index') ? 'active' : ''); ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>View Locations</p>
              </a>
            </li>
          </ul>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('settings_manage')): ?>
        <li class="nav-item">
          <a href="<?php echo e(route('admin.settings.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.settings.*') ? 'active' : ''); ?>">
            <i class="nav-icon fas fa-cog"></i>
            <p>System Settings</p>
          </a>
        </li>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('audit_logs_view')): ?>
        <li class="nav-header">AUDIT</li>
        <li class="nav-item">
          <a href="<?php echo e(route('audit-logs.index')); ?>" class="nav-link <?php echo e(request()->routeIs('audit-logs.*') ? 'active' : ''); ?>">
            <i class="nav-icon fas fa-history"></i>
            <p>Audit Logs</p>
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</aside><?php /**PATH C:\xampp\htdocs\dms-ldap\resources\views/components/sidebar.blade.php ENDPATH**/ ?>