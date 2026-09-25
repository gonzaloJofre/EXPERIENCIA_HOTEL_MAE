<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('panel') }}">
        <i class="mdi mdi-grid-large menu-icon"></i>
        <span class="menu-title">Panel</span>
        </a>
    </li>
    @if(auth()->user()->id_rol == 2)
    <li class="nav-item nav-category">Administración</li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
        <i class="menu-icon mdi mdi-floor-plan"></i>
        <span class="menu-title">Mantenedores</span>
        <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="../pages/ui-features/buttons.html">Usuarios</a></li>
            <!-- <li class="nav-item"> <a class="nav-link" href="{{ route('encuestas') }}">Encuestas</a></li> -->
        </ul>
        </div>
    </li>
    @endif
    <li class="nav-item nav-category">Reportería</li>
    <li class="nav-item">
        <a class="nav-link @if($titulo == 'Resumen General') active @endif" href="{{ route('resumen-general') }}">
        <i class="menu-icon mdi mdi-chart-line"></i>
        <span class="menu-title">Resumen General</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if($titulo == 'Indice Prioridad') active @endif" href="{{ route('indice-prioridad') }}">
        <i class="menu-icon mdi mdi-file-document"></i>
        <span class="menu-title">Indice de Prioridad</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if($titulo == 'Resultados Profesional') active @endif" href="{{ route('resultado-profesional') }}">
        <i class="menu-icon mdi mdi-account-search"></i>
        <span class="menu-title">Resultados por tipo <br> de Profesional</span>
        </a>
    </li>
    @if(auth()->user()->id_rol == 2)
    <li class="nav-item">
        <a class="nav-link @if($titulo == 'Análisis de Sentimientos') active @endif" href="{{ route('analizar_sentimientos') }}">
        <i class="menu-icon mdi mdi-comment-text-outline"></i>
        <span class="menu-title">Análisis de Sentimientos</span>
        </a>
    </li>
    @endif
    
    </ul>
</nav>