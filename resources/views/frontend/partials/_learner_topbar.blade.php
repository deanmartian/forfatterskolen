<div id="topbar">
    <div class="col-md-6">
        <h3>
            Velkommen til Forfatterskolens portal
        </h3>
    </div>
    <div class="col-md-6 text-right">
        <div class="user-image-container d-inline-block">
            <!-- User image and dropdown menu -->
            <img src="{{Auth::user()->profile_image}}" alt="User Image" id="user-image">
        </div>
        <button type="button" id="sidebarCollapse" class="btn btn-default d-xl-none">
            <span class="glyphicon glyphicon-menu-hamburger"></span>
        </button>
    </div>
</div>