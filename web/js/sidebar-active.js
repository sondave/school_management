$(function () {
    var sidebar = $('#sidebar-menu');
    if (!sidebar.length) {
        return;
    }

    sidebar.find('a').on('click', function () {
        sidebar.find('li.active').removeClass('active');

        var current = $(this).closest('li');
        current.addClass('active');
    });
});
