;(function($, window, document, undefined) {

    // Detect XMLHttpRequest support
    $.support.xhrUpload =  !!(window.XMLHttpRequest && window.File && window.FileReader
                                && window.FileList && window.Blob && window.FormData);

    // Detect getUserMedia support
    $.support.getUserMedia = !!(navigator.getUserMedia || navigator.webkitGetUserMedia ||
                                navigator.mozGetUserMedia || navigator.msGetUserMedia);

    // Detect Jcrop support
    $.support.Jcrop = !!$.Jcrop;

    // Detect JPEGCam support
    $.support.Webcam = !!window.Webcam;

    var
    // Plugin name
    pluginName = 'imgPicker',

    // Default plugin options
    defaults = {
        // Upload url (Value Type: string)
        url: 'server/upload.php',

        // DropZone (See Plugin.prototype.init())
        //dropZone: null,

        // Whether crop is enabled (Value Type: boolean)
        crop: true,

        // Aspect ratio of w/h (Value Type: decimal)
        aspectRatio: null,

        // Minimum width/height, use 0 for unbounded dimension (Value Type: array [ w, h ])
        minSize: null,

        // Maximum width/height, use 0 for unbounded dimension (Value Type: array [ w, h ])
        maxSize: null,

        // Set an initial selection area (Value Type: array [ x, y, x2, y2 ])
        setSelect: null,

        // Custom data to be passed to server (Value Type: object)
        data: {},

        // Messages
        messages: {
            selectimg: 'Please select a image to upload',
            parsererror: 'Invalid response',
            webcamerror: 'Webcam Error: ',
            uploading: 'Uploading...',
            error: 'Unexpected error',
            datauri: 'Cannot locate image format in Data URI',
            loading: 'Loading image...',
            saving: 'Saving...',
            jcrop: 'jQuery Jcrop not found',
            minCropWidth: 'Crop selection requires a minimum width of ',
            maxCropWidth: 'Crop selection exceeds maximum width of ',
            minCropHeight: 'Crop selection requires a height of ',
            maxCropHeight: 'Crop selection exceeds maximum height of ',
            img404: 'Error 404: No image was found',
            upgrade: 'This feature is not available in this browser',
        },
    },
    // HTML5 webcam stream
    stream,

    // IframeTransport iframe counter
    counter = 0;

    // Plugin constructor
    function Plugin(container, options) {
        this.options   = $.extend({}, defaults, options);
        this.container = $(container);
        this.init();
    }

    // Plugin functions
    Plugin.prototype = {

        // Initialization function
        init: function() {
            $.ajaxSetup({headers:{'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')}});

            // Add click events for the buttons
            this.elem('.ip-upload').on('change', 'input', $.proxy(this.upload, this));
            this.elem('.ip-webcam').on('click', $.proxy(this.webcam, this));
            this.elem('.ip-edit').on('click', $.proxy(this.edit, this));
            this.elem('.ip-delete').on('click', $.proxy(this._delete, this));
            this.elem('.ip-cancel').on('click', $.proxy(this.reset, this));

            if (this.options.dropZone === undefined)
                this.options.dropZone = this.container;

            var self = this,
            trigger  = $('[data-ip-modal="#'+ this.container.attr('id') +'"]');

            if (trigger.length) {
                // Modal events
                this.container.on({
                    'show.ip.modal': function() {
                        self.container.fadeIn(150, function() {
                            $(this).trigger('shown.ip.modal', self);
                        });
                    },
                    'hide.ip.modal': function() {
                        self.container.fadeOut(150, function() {
                            self.reset();
                            $(this).trigger('hidden.ip.modal', self);
                        });
                    }
                });

                // Add click event on the button to open modal
                trigger.on('click', function(e) {
                    e.preventDefault();
                    self.modal('show');
                    self.elem('.ip-close').off().on('click', function() {
                        self.modal('hide');
                    });
                });

                if (this.options.dropZone === undefined)
                    this.options.dropZone = this.elem('.ip-modal-content');
            }

            // Drag & drop upload
            if (this.options.dropZone) {
                this.options.dropZone.on('dragenter', function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                }).on('dragover', function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                }).on('drop', function(e) {
                    e.preventDefault();
                    if (e.originalEvent.dataTransfer.files && e.originalEvent.dataTransfer.files[0]) {
                        self.reset();
                        self.handleFileUpload(e.originalEvent.dataTransfer.files[0]);
                    }
                });
            }

            if (this.options.loadComplete)
                this.load();
        },

        // Modal function, show/hide modal
        modal: function(action) {
            this.container.trigger(action + '.ip.modal');
        },

        // Autoload image from server
        load: function() {
            var self = this;
            $.ajax({
                url: this.options.url,
                dataType: 'json',
                data: {_action: 'load', data: this.getData()},
                success: function(response) {
                    self.dispatch('loadComplete', response);
                }
            });
        },

        // Upload init
        upload: function(event) {
            this.reset();

            // Iframe Transport fallback
            if (!$.support.xhrUpload)
                return this.iframeTransport(event);

            // XHR Upload
            if (event.target.files && event.target.files[0])
                this.handleFileUpload(event.target.files[0]);

            $(event.target).val('');
        },

        // Iframe Transport upload
        iframeTransport: function(event) {
            var iframe, form, self = this,
            fileInput = $(event.target),
            parent = fileInput.parent(),
            fileInputClone = fileInput.clone();

            this.dispatch('uploadProgress', 100);

            // Create & add iframe to body
            iframe = $('<iframe name="iframe-transport-'+(counter+1)+'" style="display:none;"></iframe>')
            .appendTo('body')
            .on('load', function() {
                self.dispatch('uploadProgressComplete', function() {
                    try {
                        var response = $.parseJSON( iframe.contents().find('body').html() );
                    } catch(e) {}

                    // Check for response
                    if (response) {
                        self.alert('', 'hide');
                        self.uploadComplete(response);
                    } else
                        self.alert(self.i18n('parsererror'), 'error');

                    // Remove iframe & form
                    setTimeout(function() {
                        iframe.remove(); form.remove();
                        parent.append(fileInputClone);
                    }, 100);
                });
            });

            // Create form
            form = $('<form style="display:none;"><form/>');
            form.prop('method', 'POST');
            form.prop('action', this.options.url);
            form.prop('target', iframe.prop('name'));
            form.prop('enctype', 'multipart/form-data');
            form.prop('encoding', 'multipart/form-data');
            form.append(fileInput);
            form.append('<input type="hidden" name="_action" value="upload"/>');
            form.append('<input type="hidden" name="_token" value="'+$.ajaxSettings.headers['X-CSRF-Token']+'"/>');

            // Add custom data to the form
            var data = this.getData();
            if (data) {
                $.each(data, function (name, value) {
                    $('<input type="hidden"/>').prop('name', 'data['+name+']').val(value).appendTo(form);
                });
            }

            // Append the form to body and submit
            form.appendTo('body').trigger('submit');
        },

        // Webcam snapshot
        webcam: function() {
            this.reset();

            // Flash webcam fallback
            if (!$.support.getUserMedia)
                return this.alert(this.i18n('upgrade'), 'error');

            // HTML5 Webcam with <video>
            var video = $('<video autoplay></video>');
            this.elem('.ip-preview').html(video);

            window.URL = window.URL || window.webkitURL;

            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia ||
                                        navigator.mozGetUserMedia;

            var self = this;
            navigator.getUserMedia({video: true}, function(mStream) {
                video.attr('src', window.URL.createObjectURL(stream = mStream));

                self.elem('.ip-preview, .ip-cancel').show();
                self.elem('.ip-capture').on('click', function() {
                    var canvas = document.createElement('canvas'),
                        ctx    = canvas.getContext('2d');

                    canvas.width  = video[0].videoWidth;
                    canvas.height = video[0].videoHeight;
                    ctx.drawImage(video[0], 0, 0);

                    self.reset();
                    self.handleFileUpload( canvas.toDataURL('image/jpeg') );
                }).show();

            }, function(error) {
                self.alert(self.i18n('webcamerror') + error.name, 'error');
            });
        },

        // Handle file upload
        handleFileUpload: function(file) {
            // Check for file
            if (!file)
                return this.alert(this.i18n('selectimg'), 'error');

            // Check if file is ImageData string
            if (!file.name) {
                if (!file.match(/^data\:image\/(\w+)/))
                    return this.alert(this.i18n('datauri'), 'error');

                var rawImageData = file.replace(/^data\:image\/\w+\;base64\,/, '');
                file = new Blob([this.base64DecToArr(rawImageData)], {type: 'image/jpeg'});
            }

            var self = this;

            this.elem('.ip-upload input, .ip-webcam').prop('disabled', true),

            // Init XHR upload
            xhr = $.ajaxSettings.xhr();
            xhr.open('POST', this.options.url, true);

            // XHR upload progress
            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    var percentage = Math.floor((e.loaded / e.total) * 100);
                    self.dispatch('uploadProgress', percentage);
                }
            };

            // XHR upload complete
            xhr.onload = function() { setTimeout(function() {
                self.elem('.ip-upload input, .ip-webcam').prop('disabled', false);
                self.dispatch('uploadProgressComplete', function() {
                    if (xhr.status == 200) {
                        try {
                            var response = $.parseJSON(xhr.responseText);
                        } catch(e) {}

                        // Check for response
                        if (response) {
                            self.alert('', 'hide');
                            return self.uploadComplete(response);
                        }
                    }
                    self.alert(self.i18n('parsererror'), 'error');
                });
            }, 500)};

            // Create form, append file input and custom inputs
            var form = new FormData();
            form.append('_action', 'upload');
            form.append('file', file);
            $.each(this.getData(), function(name, val) {
                form.append('data['+name+']', val);
            });

            form.append('_token', $.ajaxSettings.headers['X-CSRF-Token']);

            // Send request
            xhr.send(form);
        },

        // Upload progress
        uploadProgress: function(percentage) {
            this.elem('.ip-progress').fadeIn().find('.progress-bar').css('width', percentage+'%');
        },

        // Upload progress complete
        uploadProgressComplete: function(callback) {
            this.elem('.ip-progress').fadeOut(function() {
                $(this).find('.progress-bar').css('width', '0%');
                callback();
            });
        },

        // Upload complete
        uploadComplete: function(image) {
            // Check for image erro
            if (image.error)
                return this.alert(this.i18n(image.error||'error'), 'error');

            // Dispatch uploadSuccess callback
            this.dispatch('uploadSuccess', image);

            if (this.options.crop)
                this.crop(image);
        },

        // Crop function
        crop: function(image) {
            this.reset();

            // Check if Jcrop exists
            if (!$.support.Jcrop)
                return this.dispatch('alert', this.i18n('jcrop'), 'error');

            var self = this,
            rotation = 0,
            coords,
            updateCoords = function(_coords) { coords = _coords },
            options = {onChange: updateCoords, onRelease: updateCoords, bgColor: 'white'},
            imagePreview = this.options.url + "&_token=" + $.ajaxSettings.headers['X-CSRF-Token'] + '&_action=preview&file=' + image.name + '&width=800';

            var data = this.getData();
            for (var key in data) {
                imagePreview += '&data['+key+']='+data[key];
            }

            var rotate = function(deg) {
                rotation += deg;
                if (Math.abs(rotation) >= 360) rotation = 0;
                loadjcrop(imagePreview+'&rotate='+rotation);
            };

            if (this.options.aspectRatio) options.aspectRatio = this.options.aspectRatio;
            if (this.options.setSelect) options.setSelect = this.options.setSelect;
            if (this.options.minSize) options.minSize = this.options.minSize;
            if (this.options.maxSize) options.maxSize = this.options.maxSize;

            var loadjcrop = function(imagePreview) {
                imagePreview += '&rand=' + new Date().getTime();
                self.alert(self.i18n('loading'), 'loading');

                var tmpImage = new Image();
                tmpImage.onload = function() {
                    self.alert('', 'hide');
                    self.elem('.ip-cancel').show();

                    var img = $('<img src="'+imagePreview+'" style="visibility:hidden;">');
                    self.elem('.ip-preview').html(img);

                    if (Math.abs(rotation) == 90 || Math.abs(rotation) == 270)
                        options.trueSize = [image.height, image.width];
                    else
                        options.trueSize = [image.width, image.height];

                    self.elem('.ip-preview, .ip-rotate, .ip-info, .ip-save').show();

                    img.Jcrop(options);
                };

                tmpImage.onerror = function() { self.alert(self.i18n('img404'), 'error') };
                tmpImage.src = imagePreview;
            };

            loadjcrop(imagePreview);

            // Rotate events
            this.elem('.ip-rotate-ccw').on('click', function() { rotate(-90) }).show();
            this.elem('.ip-rotate-cw').on('click', function() { rotate(90) }).show();

            // Save event
            this.elem('.ip-save').on('click', function() {
                $.ajax({
                    url: self.options.url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _action: 'crop',
                        image: image.name,
                        coords: coords,
                        rotate: rotation,
                        data: self.getData()
                    },
                    beforeSend: function() {
                        if (!self.validateCrop(coords||{})) return;

                        self.elem('.ip-save').prop('disabled', true);
                        self.alert(self.i18n('saving'), 'loading');
                    },
                    success: function(image) {
                        if (image.error)
                            return self.alert(self.i18n(image.error||'error'), 'error');
                        self.reset();
                        self.setImage(image);
                        self.dispatch('cropSuccess', image);
                    },
                    error: function() { self.alert(self.i18n('parsererror'), 'error') },
                    complete: function() { self.elem('.ip-save').prop('disabled', false) }
                });
            });
        },

        // Crop validation
        validateCrop: function(coords) {
            var  options = this.options;
            if (options.minSize) {
                if (options.minSize[0] && (coords.w||0) < options.minSize[0])
                   return this.alert(this.i18n('minCropWidth')+options.minSize[0]+'px', 'error');

                if (options.maxSize && options.maxSize[0] && (coords.w||0) > options.maxSize[0])
                    return this.alert(this.i18n('maxCropWidth')+options.maxSize[0]+'px', 'error');

                if (options.minSize[1] && (coords.h||0) < options.minSize[1])
                    return this.alert(this.i18n('minCropHeight')+options.minSize[1]+'px', 'error');

                if (options.maxSize && options.maxSize[1] && (coords.h||0) > options.maxSize[1])
                   return this.alert(this.i18n('maxCropHeight')+options.maxSize[1]+'px', 'error');
            }
            return true;
        },

        // Delete action
        _delete: function() {
            if (this.image && this.image.name)
                $.post(this.options.url, {_action: 'delete', data: this.getData(), file: this.image.name});

            this.elem('.ip-delete, .ip-edit').hide();
            this.dispatch('deleteComplete');
        },

        // Edit action
        edit: function() {
            if (this.image)
                this.crop(this.image);
        },

        // Set image object
        setImage: function(image) {
            this.image = image;
            this.elem('.ip-delete, .ip-edit').show();
        },

        // Reset everything
        reset: function() {
            this.alert('', 'hide');

            this.elem('.ip-preview').html('').fadeOut();
            this.elem('.ip-save, .ip-capture, .ip-rotate-cw, .ip-rotate-ccw').off().hide();
            this.elem('.ip-cancel, .ip-rotate, .ip-info').hide();

            if (stream) {
                try { stream.getTracks()[0].stop(); } catch (e) { }
                delete stream;
            }
        },

        // Alert function
        alert: function(message, messageType) {
            if (this.options.alert)
                return this.options.alert(message, messageType);

            var alert = this.container.find('.ip-alert');

            if (messageType == 'hide')
                return alert.hide();

            alert.html(message)
                .removeClass(
                    (messageType == 'success' ? 'alert-danger alert-warning' :
                        messageType =='warning' || messageType == 'loading' ?
                            'alert-danger alert-success' :
                                'alert-warning alert-danger')
                )
                .addClass('alert-'+ ( messageType == 'success' ? 'success' :
                            messageType =='warning' || messageType == 'loading' ?
                                'warning' : 'danger')
                ).append( $('<a class="dismiss">&times;</a>').on('click', function() { alert.hide(); }) )
                .show();
        },

        getData: function() {
            if (typeof this.options.data == 'function') {
                return this.options.data();
            }

            return this.options.data;
        },

        // Translation function
        i18n: function(message) {
            return (this.options.messages[message] || message.toString());
        },

        // Get element from container
        elem: function(selector) {
            return this.container.find(selector);
        },

        // Dispatch callbacks
        dispatch: function() {
            var name = arguments[0].replace(/^on/i, ''),
                args = Array.prototype.slice.call(arguments, 1),
                callback = this.options[name] || this[name];

            if (callback) {
                if (typeof(callback) == 'function') {
                    callback.apply(this, args);
                    return true;
                }
            }
            return false;
        },

        // Convert base64 encoded character to 6-bit integer
        b64ToUint6: function(nChr) {
            // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Base64_encoding_and_decoding
            return nChr > 64 && nChr < 91 ? nChr - 65
                : nChr > 96 && nChr < 123 ? nChr - 71
                : nChr > 47 && nChr < 58 ? nChr + 4
                : nChr === 43 ? 62 : nChr === 47 ? 63 : 0;
        },

        // Convert base64 encoded string to Uintarray
        base64DecToArr: function(sBase64, nBlocksSize) {
            // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Base64_encoding_and_decoding
            var sB64Enc = sBase64.replace(/[^A-Za-z0-9\+\/]/g, ""), nInLen = sB64Enc.length,
                nOutLen = nBlocksSize ? Math.ceil((nInLen * 3 + 1 >> 2) / nBlocksSize) * nBlocksSize : nInLen * 3 + 1 >> 2,
                taBytes = new Uint8Array(nOutLen);

            for (var nMod3, nMod4, nUint24 = 0, nOutIdx = 0, nInIdx = 0; nInIdx < nInLen; nInIdx++) {
                nMod4 = nInIdx & 3;
                nUint24 |= this.b64ToUint6(sB64Enc.charCodeAt(nInIdx)) << 18 - 6 * nMod4;
                if (nMod4 === 3 || nInLen - nInIdx === 1) {
                    for (nMod3 = 0; nMod3 < 3 && nOutIdx < nOutLen; nMod3++, nOutIdx++) {
                        taBytes[nOutIdx] = nUint24 >>> (16 >>> nMod3 & 24) & 255;
                    }
                    nUint24 = 0;
                }
            }
            return taBytes;
        }
    };

    // Plugin wrapper, prevents multiple instantiations
    $.fn[pluginName] = function(options) {
        this.each(function() {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
            }
        });

        // chain jQuery functions
        return this;
    };

})(jQuery, window, document);
