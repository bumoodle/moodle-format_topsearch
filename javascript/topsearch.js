
//redefine jquery's contains to be case insensitive
jQuery.expr[':'].Contains = function(a, i, m) 
{ 
  return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0; 
};

//start off with a null search trigger
var search_trigger = null;


function hide_all()
{
    $('.activity:visible:not(:animated)').hide();
    $('.section:visible:not(:animated)').hide();
    $('.activity').removeClass('selectTarget');
}

function unhide_all()
{
    $('.activity:hidden:not(:animated):hidden').fadeIn('fast');
    $('.section:hidden:not(:animated):hidden').fadeIn('fast');
    $('.activity').removeClass('selectTarget');
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
    }
    //otherwise
    else
    {
        //delay for a moment to ensure there's no further input, and then trigger a search
        setTimeout("do_search()", 400);
    }
}

function do_search()
{
    hide_all();

    //get the current search query
    var query = $('#topicsearch').val();

    //remove all quotes
    query = query.replace("'", "");

    //get a list of all items
    var items = $(".activity:Contains('" + query + "')");

    //add the selectTarget class to the first matching element
    items.first().addClass('selectTarget');

    //and show their parents
    items.parents(".section:not(:animated)").show();

    //show them
    items.show();


}

function enter_handler(e)
{
    if(e.which == 13)
    {
        var href = $('.selectTarget').find('a').attr('href');

        if(href != undefined)
            window.location.href = href; 
    }
}

/**
 * Initializes the topic-search module.
 */
function init_topsearch()
{
    //perform searches on changing the handler
    $('#topicsearch').keyup(search_event_handler);   
    $('#topicsearch').keypress(enter_handler);
    $('#topicsearch').focus();
}

//once the document is fully loaded, enable topic searching
$(document).ready(init_topsearch);
