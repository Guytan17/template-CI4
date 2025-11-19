// ===== IIFE: Appliquer l'état AVANT document.ready =====
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        const toggleSidebar = document.getElementById('toggle-sidebar');
        const navbarCollapse = document.querySelector('#sidebar .navbar-collapse');
        if(sidebarCollapsed) {
            const sidebarContainer = document.querySelector('.sidebar-container');
            const navbarBrand = document.querySelector('#sidebar .navbar-brand');
            const linkTexts = document.querySelectorAll('#sidebar .link-text');
            sidebarContainer.classList.remove('col-lg-2');
            sidebarContainer.classList.add('col-lg-auto');
            navbarBrand.classList.add('d-none');
            navbarCollapse.classList.remove('align-self-start');
            navbarCollapse.classList.add('align-self-center');
            linkTexts.forEach(el => el.classList.add('d-none'));
            toggleSidebar.innerHTML = '<i class="fas fa-arrow-right"></i>';
            toggleSidebar.classList.add('ms-3');
            toggleSidebar.dataset.collapse = 'true';
            updateTooltipTitle(toggleSidebar, 'Agrandir la barre latérale');
        } else {
            updateTooltipTitle(toggleSidebar, 'Réduire la barre latérale');
            navbarCollapse.classList.remove('align-self-center');
            navbarCollapse.classList.add('align-self-start');
        }
    });
})();

$(document).ready(function() {
    const $toggleSidebar = $('#toggle-sidebar');

    // ===== Toggle sidebar =====
    $toggleSidebar.on('click', function() {
        if($toggleSidebar.data('collapse') === false) {
            $('.sidebar-container').removeClass('col-lg-2').addClass('col-lg-auto');
            $toggleSidebar.data('collapse', true);
            $('#sidebar .navbar-brand').addClass('d-none');
            $('#sidebar .link-text').addClass('d-none');
            $('#sidebar .navbar-collapse').removeClass('align-self-start').addClass('align-self-center');
            $toggleSidebar.html('<i class="fas fa-arrow-right"></i>');
            $toggleSidebar.addClass('ms-3');
            updateTooltipTitle($toggleSidebar, 'Agrandir la barre latérale');
            localStorage.setItem('sidebarCollapsed', 'true');
        } else if($toggleSidebar.data('collapse') === true) {
            $toggleSidebar.removeClass('ms-3');
            $('.sidebar-container').addClass('col-lg-2').removeClass('col-lg-auto');
            $toggleSidebar.data('collapse', false);
            $('#sidebar .navbar-brand').removeClass('d-none');
            $('#sidebar .link-text').removeClass('d-none');
            $('#sidebar .navbar-collapse').removeClass('align-self-center').addClass('align-self-start');
            $toggleSidebar.html('<i class="fas fa-arrow-left"></i>');
            updateTooltipTitle($toggleSidebar, 'Réduire la barre latérale');
            localStorage.setItem('sidebarCollapsed', 'false');
        }
    });
});