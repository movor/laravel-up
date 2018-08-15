@if (Auth::check())
    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        <!-- Sidebar user panel -->
        @include('backpack::inc.sidebar_user_panel')

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
          {{-- <li class="header">{{ trans('backpack::base.administration') }}</li> --}}
          <!-- ================================================ -->
          <!-- ==== Recommended place for admin menu items ==== -->
          <!-- ================================================ -->
          <li><a href="{{ backpack_url('dashboard') }}"><i class="fa fa-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>

          <li>&nbsp;</li>

          {{-- App Specific Links --}}

          <li><a href="{{ backpack_url('article') }}"><i class="fa fa-book"></i> <span>Articles</span></a></li>
          <li><a href="{{ backpack_url('tag') }}"><i class="fa fa-tag"></i> <span>Tags</span></a></li>
          <li><a href="{{ backpack_url('user') }}"><i class="fa fa-users"></i> <span>Users</span></a></li>
          <li><a href="{{ backpack_url('newsletter') }}"><i class="fa fa-newspaper-o"></i> <span>Newsletter</span></a></li>

          <li>&nbsp;</li>

          {{-- /App Specific Links --}}

          {{-- Elfinder --}}
          <li><a href="{{  backpack_url('elfinder') }}"><i class="fa fa-image"></i> <span>File manager</span></a></li>
          <li><a href="{{ backpack_url('redirect-rule') }}"><i class="fa fa-map-signs"></i> <span>Redirect Rules</span></a></li>

          <!-- ======================================= -->
          {{-- <li class="header">Other menus</li> --}}
        </ul>
      </section>
      <!-- /.sidebar -->
    </aside>
@endif
