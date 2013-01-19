$.browser.chrome = /chrome/.test(navigator.userAgent.toLowerCase());
$.browser.ipad   = /ipad/.test(navigator.userAgent.toLowerCase());

/**
 * Initializes page contents for progressive enhancement.
 */
function initializeContents()
{
    // hide all more buttons because they are not needed with JS
    $(".element a.more").hide();

    $(".clickable.class,.clickable.interface").click(function() {
        document.location = $("a.more", this).attr('href');
    });

    // change the cursor to a pointer to make it more explicit that this it clickable
    // do a background color change on hover to emphasize the clickability eveb more
    // we do not use CSS for this because when JS is disabled this behaviour does not
    // apply and we do not want the hover
    $(".element.method,.element.function,.element.class.clickable,.element.interface.clickable")
        .css("cursor", "pointer")
        .hover(function() {
            $(this).css('backgroundColor', '#F8FDF6')
        }, function(){
            $(this).css('backgroundColor', 'white')}
        );

    // do not show tooltips on iPad; it will cause the user having to click twice
    if (!$.browser.ipad) {
        $('.btn-group.visibility,.btn-group.view,.btn-group.type-filter')
            .tooltip({'placement':'bottom'});
    }

    $('.btn-group.visibility,.btn-group.view,.btn-group.type-filter')
        .show()
        .find('button')
        .find('i').click(function(){ $(this).parent().click(); });

    // set the events for the visibility buttons and enable by default.
    $('.visibility button.public').click(function(){
        $('.element.public,.side-nav li.public').toggle($(this).hasClass('active'));
    }).click();
    $('.visibility button.protected').click(function(){
        $('.element.protected,.side-nav li.protected').toggle($(this).hasClass('active'));
    }).click();
    $('.visibility button.private').click(function(){
        $('.element.private,.side-nav li.private').toggle($(this).hasClass('active'));
    }).click();
    $('.visibility button.inherited').click(function(){
        $('.element.inherited,.side-nav li.inherited').toggle($(this).hasClass('active'));
    }).click();

    $('.type-filter button.critical').click(function(){
        $('tr.critical').toggle($(this).hasClass('active'));
    });
    $('.type-filter button.error').click(function(){
        $('tr.error').toggle($(this).hasClass('active'));
    });
    $('.type-filter button.notice').click(function(){
        $('tr.notice').toggle($(this).hasClass('active'));
    });

    $('.view button.details').click(function(){
        $('.side-nav li.view-simple').removeClass('view-simple');
    }).button('toggle').click();

    $('.view button.details').click(function(){
        $('.side-nav li.view-simple').removeClass('view-simple');
    }).button('toggle').click();
    $('.view button.simple').click(function(){
        $('.side-nav li').addClass('view-simple');
    });

// sorting example
//    $('ol li').sort(
//        function(a, b) { return a.innerHTML.toLowerCase() > b.innerHTML.toLowerCase() ? 1 : -1; }
//    ).appendTo('ol');
}

$(document).ready(function() {
    prettyPrint();

    initializeContents();

    // do not show tooltips on iPad; it will cause the user having to click twice
    if(!$.browser.ipad) {
        $(".side-nav a").tooltip({'placement': 'top'});
    }

    // chrome cannot deal with certain situations; warn the user about reduced features
    if ($.browser.chrome && (window.location.protocol == 'file:')) {
        $("body > .container").prepend(
            '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a>' +
            'You are using Google Chrome in a local environment; AJAX interaction has been ' +
            'disabled because Chrome cannot <a href="http://code.google.com/p/chromium/issues/detail?id=40787">' +
            'retrieve files using Ajax</a>.</div>'
        );
    }

    $('ul.nav-namespaces li a, ul.nav-packages li a').click(function(){
        // Google Chrome does not do Ajax locally
        if ($.browser.chrome && (window.location.protocol == 'file:'))
        {
            return true;
        }

        $(this).parents('.side-nav').find('.active').removeClass('active');
        $(this).parent().addClass('active');
        $('div.namespace-contents').load(
            this.href + ' div.namespace-contents', function(){
                initializeContents();
                $(window).scrollTop($('div.namespace-contents').position().top);
            }
        );
        $('div.package-contents').load(
            this.href + ' div.package-contents', function(){
                initializeContents();
                $(window).scrollTop($('div.package-contents').position().top);
            }
        );

        return false;
    });

    function filterPath(string)
    {
        return string
            .replace(/^\//, '')
            .replace(/(index|default).[a-zA-Z]{3,4}$/, '')
            .replace(/\/$/, '');
    }

    var locationPath = filterPath(location.pathname);

    // the ipad already smoothly scrolls and does not detect the scrollable
    // element if top=0; as such we disable this behaviour for the iPad
    if (!$.browser.ipad) {
        $('a[href*=#]').each(function ()
        {
            var thisPath = filterPath(this.pathname) || locationPath;
            if (locationPath == thisPath && (location.hostname == this.hostname || !this.hostname) && this.hash.replace(/#/, ''))
            {
                var target = decodeURIComponent(this.hash.replace(/#/,''));
                // note: I'm using attribute selector, because id selector can't match elements with '$' 
                var $target = $('[id="'+target+'"]');

                if ($target.length > 0)
                {
                    $(this).click(function (event)
                    {
                        var scrollElem = scrollableElement('html', 'body');
                        var targetOffset = $target.offset().top;

                        event.preventDefault();
                        $(scrollElem).animate({scrollTop:targetOffset}, 400, function ()
                        {
                            location.hash = target;
                        });
                    });
                }
            }
        });
    }

    // use the first element that is "scrollable"
    function scrollableElement(els)
    {
        for (var i = 0, argLength = arguments.length; i < argLength; i++)
        {
            var el = arguments[i], $scrollElement = $(el);
            if ($scrollElement.scrollTop() > 0)
            {
                return el;
            }
            else
            {
                $scrollElement.scrollTop(1);
                var isScrollable = $scrollElement.scrollTop() > 0;
                $scrollElement.scrollTop(0);
                if (isScrollable)
                {
                    return el;
                }
            }
        }
        return [];
    }
});
