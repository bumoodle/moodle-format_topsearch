var caller = null;
var courseId = null;

function set_course_id(Y, course)
{
    courseId = course;
}

function rememberCaller(item)
{
    cid = item.$trigger[0].id;
    caller = cid.substring(4);
}

function handleMenu(item)
{
    window.location.href = (M.cfg.wwwroot + "/course/mod.php?id=" + courseId + "&section=" + caller +  "&sesskey" + M.cfg.sesskey + "&add=" + item);
}

function init_context()
{
    $.contextMenu({
        selector:   '.addElementContext',
        trigger: 'left',
        items: $.contextMenu.fromMenu('#addContextMenu'),
        events: { show: rememberCaller }
    });
}


$(document).ready(init_context);
