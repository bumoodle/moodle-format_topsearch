//these are technically defined elsewhere, according to the DOM standard, but WebKit doesn't implement them (>.<)
//so they're repeated here
const KEYCODE_ENTER = 13;
const KEYCODE_UP = 38;
const KEYCODE_DOWN = 40;


//redefine jquery's contains to be case insensitive
jQuery.expr[':'].Contains = function(a, i, m) 
{ 
  return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0; 
};

//start off with a null search trigger
var search_trigger = null;
var last_search = "";


function hide_all()
{
    $('.activity:visible:not(:animated)').hide();
    $('.section:visible:not(:animated)').hide();
    $('.activity').removeClass('selectTarget');
    $('.summary').hide();
}

function unhide_all()
{
    $('.activity:hidden:not(:animated):hidden').fadeIn('fast');
    $('.section:hidden:not(:animated):hidden').fadeIn('fast');
    $('.summary').show();
    $('.activity').removeClass('selectTarget');
}

function isScrolledIntoView(elem)
{
    var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();

    var elemTop = $(elem).offset().top;
    var elemBottom = elemTop + $(elem).height();

    return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
}


function search_event_handler()
{
    //get the current search query
    var query = $('#topicsearch').val();

    //if we have a pending search trigger, cancel it
    if(search_trigger != null)
        clearTimeout(search_trigger);

    //if the search query was empty
    if(query == "")
    {
        //delay for a moment to ensure there's no further input, and then unhide all
        setTimeout("unhide_all()", 400);

        //and register that our last search was null
        last_search = "";
    }
    //otherwise
    else
    {
        //delay for a moment to ensure there's no further input, and then trigger a search
        setTimeout("do_search()", 400);
    }
}

function showMatchingSections(query)
{
    //repeat, with the section names
    var items = $(".sectionname:Contains('" + query + "'), .left:Contains('" + query + "')");

    //find their parent sections
    parents = items.parents(".section");

    //show the parents
    parents.show();

    //show all subsections to the parents
    parents.find(".section").show();

    //and then show all siblings to the matching section name:
    parents.find(".activity").show();

    //add the selectTarget class to the first matching element
    //parents.find(".activity").first().addClass('selectTarget');

}

function showMatchingActivities(query)
{
    //get a list of all _activities_ that meet our query
    var items = $(".activity:Contains('" + query + "')");

    //add the selectTarget class to the first matching element
    //$('.selectTarget').removeClass('selectTarget');
    items.first().addClass('selectTarget');

    //and show their parents
    items.parents(".section:not(:animated)").show();

    //show them
    items.show();


}

function do_search()
{

    //get the current search query
    var query = $('#topicsearch').val();

    //don't repeat searches on key events
    if(query == last_search)
        return;

   //hide all sections and activities, as a default
    hide_all();

    //remove all quotes
    query = query.replace("'", "");

    //show all _activities_ which match the query as well
    showMatchingActivities(query);

    //show all _sections_ which match the query as well
    showMatchingSections(query);

    //and keep track of the current search
    last_search = query;


}

function move_select(move_up)
{
    var new_index, current_index = null;

    //get a list of all activities
    var all = $('.activity:visible');

    //find the current activity in the list:
    
    //for each activity currently visible
    for(var i = 0; i < all.length; ++i)
    {
        //if the current item is the select target
        if($(all[i]).hasClass('selectTarget'))
        {
            //set the current index
            current_index = i;
            
            //if we're moving up, the _previous_ element is our sucessor
            if(move_up)
                new_index = i - 1;

            //otherwise, the next one is
            else
                new_index = i + 1;
        }
    }

    //if our successor is out of bounds, use a selector of zero
    if(new_index >= all.length || current_index == null)
        new_index = 0;  

    if(new_index < 0)
        new_index = (all.length) - 1;

    //remove the select index from the current item, if it has been placed
    if(current_index != null)
        $(all[current_index]).removeClass('selectTarget');

    //and move it to the next item
    $(all[new_index]).addClass('selectTarget');

    //scroll to the new item, if it's not onscreen
    if(!isScrolledIntoView(all[new_index]))
        $.scrollTo(all[new_index], 'fast');
}



function keypress_handler(e)
{
    //if we're not searching, don't steal any keys 
    if($('#topicsearch').val() == "")
        return;

    switch(e.which)
    {

        case KEYCODE_ENTER:

            //find the currently selected item
            var href = $('.selectTarget').find('a').attr('href');

            //and, if it's a link, navigate to its destination
            if(href != undefined)
                window.location.href = href; 

            break;

        case KEYCODE_UP:

            move_select(true);
            break;

        case KEYCODE_DOWN:

            move_select(false);
            break;

        
        default:
            return;

    }

    //don't propogate the event to the other DOM handlers
    return false;
}

function topsearch_lightbox(show)
{
    if(show)
    {
        $('#lightbox').fadeIn('fast');
        $('.block').fadeOut('fast');
    }
    else
    {
        $('#lightbox').fadeOut('fast');
        $('.block').fadeIn('fast');
    }
}

/**
 * Initializes the topic-search module.
 */
function init_topsearch()
{

    $("#topicsearch").show();

    //perform searches on changing the handler
    $('#topicsearch').keyup(search_event_handler);   

    //allow the up/down keys to be used for search navigation
    $('#topicsearch').keypress(keypress_handler);
    $(document).keydown(keypress_handler);
}

//once the document is fully loaded, enable topic searching
$(document).ready(init_topsearch);
