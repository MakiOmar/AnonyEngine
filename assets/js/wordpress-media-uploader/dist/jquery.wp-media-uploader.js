/*
 * Wordpress Media Uploader
 * jQuery plugin
 * https://github.com/amostajo/wordpress-media-uploader
 *
 * @author Alejandro Mostajo
 * @lincense MIT
 * @version 1.1.1
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
if (typeof jQuery === 'undefined') {
    throw new Error('Wordpress Media Gallery requires jQuery');
}
(function ($) {

    'use strict';

    /**
     * Creates plugin.
     * @since 1.0.0
     */
    $.fn.mediaUploader = function (options)
    {
        /**
         * Plugin reference.
         * @since 1.0.0
         */
        var self = this;

        /**
         * Plugin options.
         * @since 1.0.0
         */
        self.settings = $.extend({
            editor: undefined,
            render: true,
            clearTemplate: true,
            clearTarget: true,
            target: undefined,
            success: undefined,
            template: undefined
        }, options );

        /**
         * Gets and sets template in which media uploaded will be displayed.
         * @since 1.0.0
         */
        self._getTemplate = function ()
        {
            if (self.settings.template != undefined) return;
            self.settings.template = $(self).html();
            // Hide content
            if (self.settings.clearTemplate)
                $(self).html('');
        }

        /**
         * Renders media into target.
         * @since 1.0.0
         */
        self._render = function (media)
        {
            if (!self.settings.render) return;
            if (self.settings.target != undefined 
                && $(self.settings.target).length > 0
            ) {
                if (self.settings.clearTarget) $(self.settings.target).html('');
                $.each(media, function (i) {
                    $(self.settings.target).append(
                        self._parseMedia(this)
                    );
                });
            }
        }

        /**
         * Parses an individual media item and returns it as template.
         * @since 1.0.0
         * @since 1.1.0 Added embed video.
         * @since 1.1.1 Support video frame for embed videos.
         *
         * @param object media Object with media details.
         *
         * @return string html
         */
        self._parseMedia = function (media) {
            if (self.settings.template == undefined) return '';
            var html = self.settings.template;
            $.each(media, function (key) {
                html = html.replace(new RegExp('{{' + key + '}}', 'g'), media[key], html);
                html = html.replace(new RegExp('{{ ' + key + ' }}', 'g'), media[key], html);
            });
            html = $(html);
            if (media.type == 'file' && $(html).find('img').length > 0) {
                $(html).find('img').remove();
            }
            if (media.type == 'image' && $(html).find('img').length > 0) {
                $(html).find('img').attr('src', media.url);
            }
            if (media.type == 'embed' && $(html).find('img').length > 0) {
                $(html).find('img').attr('src', media.img);
            }
            return html;
        }

        /**
         * Parse HTML sent by wordpress editor.
         * Editor callback processing function.
         * @since 1.0.0
         * @since 1.1.0 Added embed video.
         *
         * @param string html HTML sent by wordpress editor.
         */
        self._parseEditorHTML = function (html)
        {
            var media = [];

            $.each($(html), function (index) {

                if ($(this).find('img').length > 0) {

                    media.push({
                        type: 'image',
                        url: $(this).find('img').attr('src'),
                        alt: $(this).find('img').attr('alt'),
                        id: $(this).find('img').attr('class').replace(/[A-Za-z\-\s]/g, '')
                    });

                } else if ($(this).is('img')) {

                    media.push({
                        type: 'image',
                        url: $(this).attr('src'),
                        alt: $(this).attr('alt'),
                        id: $(this).attr('class').replace(/[A-Za-z\-\s]/g, '')
                    });

                } else if ($(this).attr('href') != undefined) {

                    media.push({
                        type: 'file',
                        url: $(this).attr('href')
                    });

                } else if ($(this).is('iframe')) {

                    media.push({
                        type: 'embed',
                        url: $(this).attr('src'),
                        alt: $(this).attr('alt'),
                        id: $(this).attr('class').replace(/[A-Za-z\-\s]/g, ''),
                        img: $(this).attr('img')
                    });

                } else if ($(this).hasClass('video-frame')) {

                    media.push({
                        type: 'embed',
                        url: $(this).find('iframe').attr('src'),
                        alt: $(this).find('iframe').attr('alt'),
                        id: $(this).find('iframe').attr('class').replace(/[A-Za-z\-\s]/g, ''),
                        img: $(this).find('iframe').attr('img')
                    });

                }

            });

            self._render(media);

            if (self.settings.success != undefined) {
                self.settings.success(media);
            }
        };

        /**
         * Saves previous event.
         * @since 1.0.2
         * @var object
         */
        self.prev_send_to_editor = window.send_to_editor;

        /**
         * Wordpress editor callback.
         * @since 1.0.0
         * @since 1.0.2 Fixes multiple callers.
         *
         * @param string html   HTML returned by wordpress editor.
         * @param string editor Editor key name.
         */
        window.send_to_editor = function (html, editor)
        {
            if (self.settings.editor == undefined
                || (this.activeEditor != self.settings.editor
                && editor != self.settings.editor)
            )
                return self.prev_send_to_editor == undefined
                    ? undefined
                    : self.prev_send_to_editor(
                        html,
                        this.activeEditor == undefined ? editor : this.activeEditor
                    );

            self._parseEditorHTML(html);
        };

        /**
         * Gets template.
         * @since 1.0.0 
         */
        self._getTemplate();

        /**
         * Adds media to plugin.
         * Thought to accessed as public method.
         * @since 1.0.4
         *
         * @param mixed mediaToAdd
         */
        self._add_media = function(mediaToAdd)
        {
            var media = [];

            // Convert param to array
            if (mediaToAdd.constructor !== Array) {
                var item = mediaToAdd;
                mediaToAdd = [];
                mediaToAdd.push(item);
            }
            for (var i in mediaToAdd) {
                var item = {};
                if (mediaToAdd[i].type === undefined
                    && mediaToAdd[i].url === undefined
                )
                    continue;
                item.type = mediaToAdd[i].type;
                item.url = mediaToAdd[i].url;
                if (mediaToAdd[i].alt !== undefined)
                    item.alt = mediaToAdd[i].alt;
                if (mediaToAdd[i].id !== undefined)
                    item.id = mediaToAdd[i].id;
                media.push(item);
            }

            self._render(media);

            if (self.settings.success != undefined) {
                self.settings.success(media);
            }
        }

        /**
         * Public method alias for add media function.
         * @since 1.0.4
         */
        this.add = self._add_media;

        return this;
    };
}(jQuery));