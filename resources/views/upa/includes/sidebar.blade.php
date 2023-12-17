<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <!-- Sidebar -->
    <div class="sidebar">
        <ul class="pt-3 nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-item">
                <a href="{{route('upa.main.index')}}" class="nav-link">
                    <i class="nav-icon fas fa-home"></i>
                    <p>
                        Главная
                    </p>
                </a>
            </li>
            @can('view-admin', auth()->user())
                <li class="nav-item">
                    <a href="{{route('upa.comment.index')}}" class="nav-link">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>
                            Замечания
                            <!-- <message-comment :authoperator='@json($auth_operator)' ref="comm"></message-comment> -->
                        </p>
                    </a>
                </li>
            @endcan
            @canany(['view-controller','view'], auth()->user())
            <li class="nav-item">
                <a href="{{route('upa.control.index')}}" class="nav-link">
                    <i class="nav-icon fas fa-book"></i>
                    <p>
                        Оценка
                        <message-control :authoperator='@json($auth_operator)' ref="cont"></message-control>
                    </p>
                </a>
            </li>
            @endcan
            @canany(['view-controller','view'], auth()->user())
            <li class="nav-item">
                <a href="{{route('upa.leader.index')}}" class="nav-link">
                    <i class="nav-icon fas fa-bolt"></i>
                    <p>
                        Проработка
                        <message-leader :authoperator='@json($auth_operator)' ref="lead"></message-leader>
                    </p>
                </a>
            </li>
            @endcan
            @canany('view-admin', auth()->user())
            <li class="nav-item">
                <a href="{{route('upa.motivation.index')}}" class="nav-link">
                    <i class="nav-icon fas fa-fist-raised"></i>
                    <p>
                        Мотивация
                    </p>
                </a>
            </li>
            @endcan
            @can('view', auth()->user())
            <li class="nav-item">
                <a href="{{route('upa.helper.index')}}" class="nav-link">
                    <i class="nav-icon fas fa-question"></i>
                    <p>
                        Хелпер
                    </p>
                </a>
            </li>
            @endcan
            @canany(['view-admin','view-controller','view-journal','view'], auth()->user())
            <li class="nav-item">
                <a href="{{route('upa.journal.index')}}" class="nav-link">
                    <i class="nav-icon fas fa-book-open"></i>
                    <p>
                        Журнал
                    </p>
                </a>
            </li>
            @endcan
        </ul>
    </div>
    <!-- /.sidebar -->
</aside>
