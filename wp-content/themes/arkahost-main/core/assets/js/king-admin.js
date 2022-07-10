(function($) {
    $(document).ready(function() {

        //disabled font-awesome visualcomposer
        $('link[href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css"]').prop('disabled', true);

        $('#visual_composer_content').dblclick(function(e) {
            if ($(e.target).hasClass('vc_controls-row')) {
                if ($(e.target).parent().hasClass('wpb_vc_row')) {
                    var row = $(e.target).parent();
                    if (row.parent().attr('id') == 'visual_composer_content') {
                        var modelId = row.data('model-id');
                        var data = vc.storage.data[modelId];

                        //console.log( data.html );
                        var tags = 'vc_raw_html';
                        var regx = new RegExp('\\[(\\[?)(' + tags + ')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)', 'g');
                        var uri = new RegExp(site_uri, 'g');
                        var raw = data.html.replace(regx, function(m, n, i, j, k, content) {

                            return '[vc_raw_html]' + Base64.encode(escape(unescape(Base64.decode(content)).replace(uri, '<?php echo SITE_URI; ?>'))) + '[/vc_raw_html]';

                        });
                        raw = raw.replace(uri, '%SITE_URI%');
                        copy2clipboard(raw);
                        document.title = '(√)' + document.title;
                        setTimeout(function() {
                            document.title = document.title.replace('(√)', '');
                        }, 300);
                    }
                }
            }
        });

        $('.vc_buttons').append('<a href="#" id="king-online-sections" class="king-online-sections" title="Click to install sections of theme"></a>');
        $('.vc_welcome-visible-ne').append('<a href="#" id="king-online-sections" class="king-online-sections" title="Click to install sections of theme"></a>');
        $('.vc_ui-btn-group.vc_welcome-visible-e').append('<a href="#" id="king-online-sections-2" title="Click to install sections of theme"  class="king-online-sections blank_page"></a>');

        $('body').append('<div id="king-theme-sections" class="popup-overlay" style="display: none;"><div class="popup"><div class="popup-head">' +
            '<h3 class="poptit">Install ' + theme_name + ' Theme Sections</h3>' +
            '<a class="alignright btn btn_normal popclose" href="#">' +
            '<i class="fas fa-times"></i> Close' +
            '</a></div><div class="popup-body scroll"> </div></div></div>');
        $('#king-theme-sections .popclose').click(function(e) {
            $('#king-theme-sections').animate({ opacity: 0 }, function() {
                $('#king-theme-sections').css({ display: 'none' });
            });
            e.preventDefault();
        });
        $('#king-theme-sections').click(function(e) {
            if (e.target) {
                if (e.target.id == 'king-theme-sections') {
                    $('#king-theme-sections .popclose').click();
                }
            }
        });
        $('.king-online-sections').click(function(e) {
            $('#king-theme-sections').css({ display: 'block', opacity: 0 }).animate({ opacity: 1 });
            e.preventDefault();
            if ($('#king-theme-sections').data('sections-loaded') != true) {
                $('#king-theme-sections').attr('data-sections-loaded', 'true');
                $('#king-theme-sections .popup-body').html('<br /><br /><i class="fa fa-spinner fa-spin" style="font-size: 25px;"></i><br /><br />Loading sections from server');
                sections.loadFromServer();
            }
        });
    });

})(jQuery);



function shortcode_process(input, callback, tags) {

    var regx = new RegExp('\\[(\\[?)(' + tags + ')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)', 'g'),
        result, agrs;

    var split_arguments = /([a-zA-Z0-9\-\_]+)="([^"]+)+"/gi;

    while (result = regx.exec(input)) {

        var paramesArg = [];
        while (agrs = split_arguments.exec(result[3])) { paramesArg[agrs[1]] = agrs[2]; }

        var args = {
            full: result[0],
            name: result[2],
            parames: result[3],
            content: result[5],
            arguments: paramesArg,
            input: input,
            result: result
        }

        callback(args);

    }

}

var sections = {

    loadFromServer: function(page) {

        page = page != undefined ? page : 0;

        jQuery.post(site_uri + '/wp-admin/admin-ajax.php', {
            'action': 'loadSectionsSample',
            'page': page
        }, function(result) {
            var $ = jQuery;
            if (page == 0) {
                $('#king-theme-sections .popup-body').html(result);
            } else {
                $('#king-theme-sections .popup-body .loadMore').remove();
                $('#sectionsStore').append(result);
            }
            $('#sectionsStore .installSection').unbind('click').bind('click', function() {

                this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Installing ';
                this.disabled = true;
                $(this).addClass('installing');

                var rel = $(this).attr('rel');

                jQuery.post(ajaxurl, {
                    'action': 'loadSectionsSample',
                    'install': rel
                }, function(result) {

                    sections.install(Base64.decode(result.trim()));

                });

            });
            $('#sectionsStore .loadMore').unbind('click').bind('click', function() {
                var p = $(this).attr('rel');
                this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';
                this.disabled = true;
                sections.loadFromServer(p);
            });
        });

    },

    install: function(data) {

        var $ = jQuery;

        $('#sectionsStore .installing').each(function() {
            $(this).attr({ disabled: false }).html('<i class="fas fa-cloud-download-alt"></i> Install').removeClass('installing');
        });

        var scrollTop = jQuery('body').scrollTop();

        data = data.replace(/%SITE_URI%/g, site_uri);

        /* Re init VC with new data added */
        vc.storage.setContent(vc.storage.getContent() + data);
        vc.storage.checksum = false;
        vc.app.show();
        /* End of re-init */

        $('#king-theme-sections').hide();

        setTimeout(function() {
            jQuery('body').animate({ scrollTop: (scrollTop + 150) });
        }, 800);

    }
}


function copy2clipboard(str) {
    document.oncopy = function(event) {
        event.clipboardData.setData('text', str);
        event.preventDefault();
    };
    document.execCommand("Copy", false, null);
}