<div class="community-nav">
    <a href="{{ route('learner.community.home') }}" class="{{ ($activePage ?? '') === 'home' ? 'active' : '' }}">
        <i class="fa fa-home"></i> Hjem
    </a>
    <a href="{{ route('learner.community.discussions') }}" class="{{ ($activePage ?? '') === 'discussions' ? 'active' : '' }}">
        <i class="fa fa-comments"></i> Diskusjoner
    </a>
    <a href="{{ route('learner.community.messages') }}" class="{{ ($activePage ?? '') === 'messages' ? 'active' : '' }}">
        <i class="fa fa-envelope"></i> Meldinger
        @if(($unreadMessages ?? 0) > 0)
            <span class="badge badge-danger">{{ $unreadMessages }}</span>
        @endif
    </a>
    <a href="{{ route('learner.community.members') }}" class="{{ ($activePage ?? '') === 'members' ? 'active' : '' }}">
        <i class="fa fa-users"></i> Medlemmer
    </a>
    <a href="{{ route('learner.community.manuscripts') }}" class="{{ ($activePage ?? '') === 'manuscripts' ? 'active' : '' }}">
        <i class="fa fa-book"></i> Manusrom
    </a>
    <a href="{{ route('learner.community.notifications') }}" class="{{ ($activePage ?? '') === 'notifications' ? 'active' : '' }}">
        <i class="fa fa-bell"></i> Varsler
        @if(($unreadNotifications ?? 0) > 0)
            <span class="badge badge-danger">{{ $unreadNotifications }}</span>
        @endif
    </a>
    <a href="{{ route('learner.community.courseGroups') }}" class="{{ ($activePage ?? '') === 'courseGroups' ? 'active' : '' }}">
        <i class="fa fa-graduation-cap"></i> Kursgrupper
    </a>
    <a href="{{ route('learner.community.profile') }}" class="{{ ($activePage ?? '') === 'profile' ? 'active' : '' }}">
        <i class="fa fa-user"></i> Min profil
    </a>
</div>
