(function($, Models, Collections, Views) {
    $(document).ready(function() {
        Models.MessageChatBox = Backbone.Model.extend({
            action: 'ae-sync-message-chat-box',
            initialize: function() {}
        });
        Collections.MessagesChatBox = Backbone.Collection.extend({
            model: Models.MessageChatBox,
            action: 'ae-fetch-messages-chat-box',
            initialize: function() {
                this.paged = 1;
            },
            comparator: function(m) {
                // var jobDate = new Date(m.get('comment_date'));
                // return -jobDate.getTime();
                return -m.get('ID');
            }
        });
        MessageItemChatBox = Views.PostItem.extend({
            tagName: 'li',
            className: 'message-item',
            template: _.template($('#ae-message-loop').html()),
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                var view = this;
                // after render view
                if( ae_globals.user_ID != this.model.get('user_id') ){
                    view.$el.addClass('partner-message-item');
                }
                else{
                    view.$el.find('.avatar-chat-wrapper').remove();
                }
            }
        });
        ListMessageChatBox = Views.ListPost.extend({
            events : {
                'click .removeAtt': 'removeAtt'
            },            
            tagName: 'li',
            itemView: MessageItemChatBox,
            itemClass: 'message-item',            
            removeAtt : function(e){
                if (confirm(ae_globals.confirm_message)) {
                    e.preventDefault();
                    var post_id = $(e.currentTarget).attr('data-id');
                    var data = {
                        'action': 'free_remove_attack_file',
                        'post_id': post_id,                                
                    };    
                    jQuery.ajax({
                        type: "POST",
                        url: ae_globals.ajaxURL,
                        data: data,
                        action: 'free_remove_attack_file',
                        success: function(data) {
                            if(data!=="0"){
                                AE.pubsub.trigger('ae:notification', {
                                    msg: fre_fronts.deleted_file_successfully,
                                    notice_type: 'success',
                                });
                                setTimeout(function(){
                                    location.reload();
                                }, 3000);
                            }                        
                            else{
                                AE.pubsub.trigger('ae:notification', {
                                    msg: fre_fronts.failed_deleted_file,
                                    notice_type: 'error',
                                });
                                etTimeout(function(){
                                    location.reload();
                                }, 3000);
                            }                     
                        }
                    });
                }
            }                  
            // appendHtml: function(cv, iv, index) {
            //     var post = index,
            //         $existingItems = cv.$('li.message-item'),
            //         index = (index) ? index : $existingItems.length,
            //         position = $existingItems.eq(index - 1),
            //         $itemView = $(iv.el);
            //     if (!post || position.length === 0) {
            //         cv.$el.prepend(iv.el);
            //     } else {
            //         $itemView.insertAfter(position);
            //     }
            // }
        });
        // view control file upload
        Views.FileUploaderChatBox = Backbone.View.extend({
            events: {
                'click .removeFile': 'removeFile'
            },
            fileIDs : [],
            docs_uploader : {},
            initialize: function(options) {
                _.bindAll(this, 'refresh');
                var view = this,
                    $apply_docs = this.$el,
                    uploaderID = options.uploaderID;
                view.blockUi = new Views.BlockUi();
                // this.fileIDs = options.fileIDs;
                // this.docs_uploader = options.docs_uploader;
                this.docs_uploader = new AE.Views.File_Uploader({
                    el: $apply_docs,
                    uploaderID: uploaderID,
                    multi_selection: true,
                    unique_names: true,
                    upload_later: true,
                    filters: [{
                        title: "Compressed Files",
                        extensions: 'zip,rar'
                    }, {
                        title: 'Documents',
                        extensions: 'txt,gif,jpeg,jpg,png,doc,docx,pt,pptx,pdf,chm,xls,xlsx,txt' //'pdf,doc,docx,png,jpg,gif'
                    }],
                    multipart_params: {
                        _ajax_nonce: $apply_docs.find('.et_ajaxnonce').attr('id'),
                        action: 'ae_upload_files',
                        imgType : 'file'
                    },
                    cbAdded: function(up, files) {
                        var $file_list = view.$('.apply_docs_file_list'),
                            i;
                        // Check if the size of the queue is over MAX_FILE_COUNT
                        if (up.files.length > view.docs_uploader.MAX_FILE_COUNT) {
                            // Removing the extra files
                            while (up.files.length > view.docs_uploader.MAX_FILE_COUNT) {
                                up.removeFile(up.files[up.files.length - 1]);
                            }
                        }
                        // render the file list again
                        $file_list.empty();
                        for (i = 0; i < up.files.length; i++) {
                            $(view.fileTemplate({
                                id: up.files[i].id,
                                filename: up.files[i].name,
                                filesize: plupload.formatSize(up.files[i].size),
                                percent: up.files[i].percent
                            })).appendTo($file_list);
                        }
                    },
                    cbRemoved: function(up, files) {
                        for (var i = 0; i < files.length; i++) {
                            view.$('#' + files[i].id).remove();
                        }
                    },
                    onProgress: function(up, file) {
                        view.$('#' + file.id + " .percent").html(file.percent + "%");
                    },
                    cbUploaded: function(up, file, res) {
                        if (res.success) {
                            view.fileIDs.push(res.data);
                        } else {
                            // assign a flag to know that we are having errors
                            view.hasUploadError = true;
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    },
                    onError: function(up, err) {
                        AE.pubsub.trigger('ae:notification', {
                            msg: err.message,
                            notice_type: 'error'
                        });
                    },
                    beforeSend: function() {
                        view.blockUi.block($apply_docs);
                    },
                    success: function() {
                        view.blockUi.unblock();
                    }
                });

                // setup the maximum files allowed to attach in an application
                this.docs_uploader.MAX_FILE_COUNT = 3;
            },
            fileTemplate: _.template('<li id="{{=id}}"><span class="file-name" >{{=filename }}</span><a href="#"><i class="fa fa-times removeFile"></i></a></li>'),
            removeFile: function(e) {
                e.preventDefault();
                var fileID = $(e.currentTarget).closest('li').attr("id");
                for (i = 0; i < this.docs_uploader.controller.files.length; i++) {
                    if (this.docs_uploader.controller.files[i].id === fileID) {
                        this.docs_uploader.controller.removeFile(this.docs_uploader.controller.files[i]);
                    }
                }
            },
            removeAllFile : function(){
                var view = this;
                $.each(view.docs_uploader.controller.files, function (i, file) {
                    view.docs_uploader.controller.removeFile(file);
                });
            },
            refresh : function(){
                this.$('.apply_docs_file_list').html('');
                this.fileIDs = [];
            }
        });
        /**
         * project workspace control
         * @since 1.3
         * @author Dakachi
         */
        Views.WorkPlacesChatBox = Backbone.View.extend({
            events: {
                'submit form.form-message': 'submitAttach'
            },
            initialize: function(options) {
                var view = this;
                view.blockUi = new Views.BlockUi();
                if ($('.message-container-chat-box').find('.postdata').length > 0) {
                    var postsdata = JSON.parse($('.message-container-chat-box').find('.postdata').html());
                    view.messages = new Collections.MessagesChatBox(postsdata);
                } else {
                    view.messages = new Collections.MessagesChatBox();
                }
                /**
                 * init list blog view
                 */
                this.listMessages = new ListMessageChatBox({
                    itemView: MessageItemChatBox,
                    collection: view.messages,
                    el: $('.message-container-chat-box').find('.list-chat-work-place')
                });
                /**
                 * init block control list blog
                 */
                this.blockCT = new Views.BlockControl({
                    collection: view.messages,
                    el: $('.message-container-chat-box')
                });
                // init upload file control
                this.docs_uploader = {};
                this.filecontroller = new Views.FileUploaderChatBox({
                    el: $('#file-container-chat-box'),
                    uploaderID: 'apply_docs_chat_box',
                    fileIDs : []
                });
                this.docs_uploader = this.filecontroller.docs_uploader;
                this.liveShowMsg();

                // Fetch changelog
                AE.pubsub.on( 'ae:addChangelog', this.fetchChangelog, this );
            },
            fetchChangelog: function() {
                this.fetchListMessage();
            },
            submitAttach: function(e) {
                var self = this;
                var uploaded = false,
                    $target = $(e.currentTarget);
                e.preventDefault();
                if (this.docs_uploader.controller.files.length > 0) {
                    this.docs_uploader.controller.bind('StateChanged', function(up) {
                        if (up.files.length === up.total.uploaded) {
                            // if no errors, post the form
                            if (!self.hasUploadError && !uploaded) {
                                self.sendMessage($target);
                                uploaded = true;
                            }
                        }
                    });
                    this.hasUploadError = false; // reset the flag before re-upload
                    this.docs_uploader.controller.start();
                } else {
                    this.sendMessage($target);
                }
            },
            sendMessage: function(target) {
                var message = new Models.MessageChatBox(),
                    view = this,
                    $target = target;
                $target.find('textarea, input, select').each(function() {
                    message.set($(this).attr('name'), $(this).val());
                });
                message.set('fileID' , this.filecontroller.fileIDs);

                this.filecontroller.fileIDs = [];
                message.save('', '', {
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(result, res, xhr) {
                        view.blockUi.unblock();
                        view.$('textarea').val('');
                        view.$('textarea').height(38);
                        view.docs_uploader.controller.splice();
                        view.docs_uploader.controller.refresh();
                        if (res.success) {
                            view.messages.add(message);
                            view.listMessages.render();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            },
            fetchListMessage: function() {
                var view = this;
                if( $('#workspace_query_args_chat_box').length > 0 ){
                    view.blockCT.query = JSON.parse($('#workspace_query_args_chat_box').html());
                    $target = $('.message-container-chat-box').find('.list-chat-work-place');
                    view.blockCT.query['use_heartbeat'] = 1;
                    view.blockCT.page = 1;
                    view.blockCT.fetch($target);
                }
            },
            liveShowMsg: function(){
                var view = this;
                view.initHeartBeat();
                $(document).on( 'heartbeat-tick', function( event, data ) {
                    if ( data.hasOwnProperty( 'new_message' ) ) {
                        if( $('#workspace_query_args_chat_box').length > 0 ){
                            if( data['new_message'] == "chat_box" ){
                                view.fetchListMessage();
                            }
                        }
                    }
                });
            },
            initHeartBeat: function(){
                 $(document).on('heartbeat-send', function(e, data) {
                    if( $('#workspace_query_args_chat_box').length > 0 ){
                        var qr = JSON.parse($('#workspace_query_args_chat_box').html());
                        if(typeof qr['post_id'] !== 'undefined'){
                            data['new_message'] = qr['post_id'];
                        }
                    }
                });
            }

        });
        new Views.WorkPlacesChatBox({
            el: 'div.workplace-details-chat-box'
        });
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		Models.MessageTranslatorBox = Backbone.Model.extend({
            action: 'ae-sync-message-translator-box',
            initialize: function() {}
        });
        Collections.MessagesTranslatorBox = Backbone.Collection.extend({
            model: Models.MessageTranslatorBox,
            action: 'ae-fetch-messages-translator-box',
            initialize: function() {
                this.paged = 1;
            },
            comparator: function(m) {
                // var jobDate = new Date(m.get('comment_date'));
                // return -jobDate.getTime();
                return -m.get('ID');
            }
        });
        MessageItemTranslatorBox = Views.PostItem.extend({
            tagName: 'li',
            className: 'message-item',
            template: _.template($('#ae-message-loop').html()),
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                var view = this;
                // after render view
                if( ae_globals.user_ID != this.model.get('user_id') ){
                    view.$el.addClass('partner-message-item');
                }
                else{
                    view.$el.find('.avatar-chat-wrapper').remove();
                }
            }
        });
        ListMessageTranslatorBox = Views.ListPost.extend({
            events : {
                'click .removeAtt': 'removeAtt'
            },            
            tagName: 'li',
            itemView: MessageItemTranslatorBox,
            itemClass: 'message-item',            
            removeAtt : function(e){
                if (confirm(ae_globals.confirm_message)) {
                    e.preventDefault();
                    var post_id = $(e.currentTarget).attr('data-id');
                    var data = {
                        'action': 'free_remove_attack_file',
                        'post_id': post_id,                                
                    };    
                    jQuery.ajax({
                        type: "POST",
                        url: ae_globals.ajaxURL,
                        data: data,
                        action: 'free_remove_attack_file',
                        success: function(data) {
                            if(data!=="0"){
                                AE.pubsub.trigger('ae:notification', {
                                    msg: fre_fronts.deleted_file_successfully,
                                    notice_type: 'success',
                                });
                                setTimeout(function(){
                                    location.reload();
                                }, 3000);
                            }                        
                            else{
                                AE.pubsub.trigger('ae:notification', {
                                    msg: fre_fronts.failed_deleted_file,
                                    notice_type: 'error',
                                });
                                etTimeout(function(){
                                    location.reload();
                                }, 3000);
                            }                     
                        }
                    });
                }
            }                  
            // appendHtml: function(cv, iv, index) {
            //     var post = index,
            //         $existingItems = cv.$('li.message-item'),
            //         index = (index) ? index : $existingItems.length,
            //         position = $existingItems.eq(index - 1),
            //         $itemView = $(iv.el);
            //     if (!post || position.length === 0) {
            //         cv.$el.prepend(iv.el);
            //     } else {
            //         $itemView.insertAfter(position);
            //     }
            // }
        });
        // view control file upload
        Views.FileUploaderTranslatorBox = Backbone.View.extend({
            events: {
                'click .removeFile': 'removeFile'
            },
            fileIDs : [],
            docs_uploader : {},
            initialize: function(options) {
                _.bindAll(this, 'refresh');
                var view = this,
                    $apply_docs = this.$el,
                    uploaderID = options.uploaderID;
                view.blockUi = new Views.BlockUi();
                // this.fileIDs = options.fileIDs;
                // this.docs_uploader = options.docs_uploader;
                this.docs_uploader = new AE.Views.File_Uploader({
                    el: $apply_docs,
                    uploaderID: uploaderID,
                    multi_selection: true,
                    unique_names: true,
                    upload_later: true,
                    filters: [{
                        title: "Compressed Files",
                        extensions: 'zip,rar'
                    }, {
                        title: 'Documents',
                        extensions: 'txt,gif,jped,jpg,png,doc,docx,pt,pptx,pdf,chm,xls,xlsx,txt' //'pdf,doc,docx,png,jpg,gif'
                    }],
                    multipart_params: {
                        _ajax_nonce: $apply_docs.find('.et_ajaxnonce').attr('id'),
                        action: 'ae_upload_files',
                        imgType : 'file'
                    },
                    cbAdded: function(up, files) {
                        var $file_list = view.$('.apply_docs_file_list'),
                            i;
                        // Check if the size of the queue is over MAX_FILE_COUNT
                        if (up.files.length > view.docs_uploader.MAX_FILE_COUNT) {
                            // Removing the extra files
                            while (up.files.length > view.docs_uploader.MAX_FILE_COUNT) {
                                up.removeFile(up.files[up.files.length - 1]);
                            }
                        }
                        // render the file list again
                        $file_list.empty();
                        for (i = 0; i < up.files.length; i++) {
                            $(view.fileTemplate({
                                id: up.files[i].id,
                                filename: up.files[i].name,
                                filesize: plupload.formatSize(up.files[i].size),
                                percent: up.files[i].percent
                            })).appendTo($file_list);
                        }
                    },
                    cbRemoved: function(up, files) {
                        for (var i = 0; i < files.length; i++) {
                            view.$('#' + files[i].id).remove();
                        }
                    },
                    onProgress: function(up, file) {
                        view.$('#' + file.id + " .percent").html(file.percent + "%");
                    },
                    cbUploaded: function(up, file, res) {
                        if (res.success) {
                            view.fileIDs.push(res.data);
                        } else {
                            // assign a flag to know that we are having errors
                            view.hasUploadError = true;
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    },
                    onError: function(up, err) {
                        AE.pubsub.trigger('ae:notification', {
                            msg: err.message,
                            notice_type: 'error'
                        });
                    },
                    beforeSend: function() {
                        view.blockUi.block($apply_docs);
                    },
                    success: function() {
                        view.blockUi.unblock();
                    }
                });

                // setup the maximum files allowed to attach in an application
                this.docs_uploader.MAX_FILE_COUNT = 3;
            },
            fileTemplate: _.template('<li id="{{=id}}"><span class="file-name" >{{=filename }}</span><a href="#"><i class="fa fa-times removeFile"></i></a></li>'),
            removeFile: function(e) {
                e.preventDefault();
                var fileID = $(e.currentTarget).closest('li').attr("id");
                for (i = 0; i < this.docs_uploader.controller.files.length; i++) {
                    if (this.docs_uploader.controller.files[i].id === fileID) {
                        this.docs_uploader.controller.removeFile(this.docs_uploader.controller.files[i]);
                    }
                }
            },
            removeAllFile : function(){
                var view = this;
                $.each(view.docs_uploader.controller.files, function (i, file) {
                    view.docs_uploader.controller.removeFile(file);
                });
            },
            refresh : function(){
                this.$('.apply_docs_file_list').html('');
                this.fileIDs = [];
            }
        });
		Views.WorkPlacesTranslatorBox = Backbone.View.extend({
            events: {
                'submit form.form-message': 'submitAttach'
            },
            initialize: function(options) {
                var view = this;
                view.blockUi = new Views.BlockUi();
                if ($('.message-container-translator-box').find('.postdata').length > 0) {
                    var postsdata = JSON.parse($('.message-container-translator-box').find('.postdata').html());
                    view.messages = new Collections.MessagesTranslatorBox(postsdata);
                } else {
                    view.messages = new Collections.MessagesTranslatorBox();
                }
                /**
                 * init list blog view
                 */
                this.listMessages = new ListMessageTranslatorBox({
                    itemView: MessageItemTranslatorBox,
                    collection: view.messages,
                    el: $('.message-container-translator-box').find('.list-chat-work-place')
                });
                /**
                 * init block control list blog
                 */
                this.blockCT = new Views.BlockControl({
                    collection: view.messages,
                    el: $('.message-container-translator-box')
                });
                // init upload file control
                this.docs_uploader = {};
                this.filecontroller = new Views.FileUploaderTranslatorBox({
                    el: $('#file-container-translator-box'),
                    uploaderID: 'apply_docs',
                    fileIDs : []
                });
                this.docs_uploader = this.filecontroller.docs_uploader;
                this.liveShowMsg();

                // Fetch changelog
                AE.pubsub.on( 'ae:addChangelog', this.fetchChangelog, this );
            },
            fetchChangelog: function() {
                this.fetchListMessage();
            },
            submitAttach: function(e) {
                var self = this;
                var uploaded = false,
                    $target = $(e.currentTarget);
                e.preventDefault();
                if (this.docs_uploader.controller.files.length > 0) {
                    this.docs_uploader.controller.bind('StateChanged', function(up) {
                        if (up.files.length === up.total.uploaded) {
                            // if no errors, post the form
                            if (!self.hasUploadError && !uploaded) {
                                self.sendMessage($target);
                                uploaded = true;
                            }
                        }
                    });
                    this.hasUploadError = false; // reset the flag before re-upload
                    this.docs_uploader.controller.start();
                } else {
                    this.sendMessage($target);
                }
            },
            sendMessage: function(target) {
                var message = new Models.MessageTranslatorBox(),
                    view = this,
                    $target = target;
                $target.find('textarea, input, select').each(function() {
                    message.set($(this).attr('name'), $(this).val());
                });
                message.set('fileID' , this.filecontroller.fileIDs);

                this.filecontroller.fileIDs = [];
                message.save('', '', {
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(result, res, xhr) {
                        view.blockUi.unblock();
                        view.$('textarea').val('');
                        view.$('textarea').height(38);
                        view.docs_uploader.controller.splice();
                        view.docs_uploader.controller.refresh();
                        if (res.success) {
                            view.messages.add(message);
                            view.listMessages.render();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            },
            fetchListMessage: function() {
                var view = this;
                if( $('#workspace_query_args_translator_box').length > 0 ){
                    view.blockCT.query = JSON.parse($('#workspace_query_args_translator_box').html());
                    $target = $('.message-container-translator-box').find('.list-chat-work-place');
                    view.blockCT.query['use_heartbeat'] = 1;
                    view.blockCT.page = 1;
                    view.blockCT.fetch($target);
                }
            },
            liveShowMsg: function(){
                var view = this;
                view.initHeartBeat();
                $(document).on( 'heartbeat-tick', function( event, data ) {
                    if ( data.hasOwnProperty( 'new_message' ) ) {
                        if( $('#workspace_query_args_translator_box').length > 0 ){
                            if( data['new_message'] == "translator_box" ){
                                view.fetchListMessage();
                            }
                        }
                    }
                });
            },
            initHeartBeat: function(){
                 $(document).on('heartbeat-send', function(e, data) {
                    if( $('#workspace_query_args_translator_box').length > 0 ){
                        var qr = JSON.parse($('#workspace_query_args_translator_box').html());
                        if(typeof qr['post_id'] !== 'undefined'){
                            data['new_message'] = qr['post_id'];
                        }
                    }
                });
            }

        });
        new Views.WorkPlacesTranslatorBox({
            el: 'div.workplace-details-translator-box'
        });
    })
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);








































































/**
 * report control view
 * @author Dakachi
 */
(function($, Models, Collections, Views) {
    $(document).ready(function() {
        Models.Report = Backbone.Model.extend({
            action: 'ae-sync-report',
            initialize: function() {}
        });
        Collections.Reports = Backbone.Collection.extend({
            model: Models.Message,
            action: 'ae-fetch-reports',
            initialize: function() {
                this.paged = 1;
            },
            comparator: function(m) {
                // console.log(m);
                // var jobDate = new Date(m.get('comment_date'));
                // return -jobDate.getTime();
                return -m.get('ID');
            }
        });
        ReportItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'message-item',
            template: _.template($('#ae-report-loop').html()),
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                // after render view
            }
        });
        ListReport = Views.ListPost.extend({
            tagName: 'li',
            itemView: MessageItemChatBox,
            itemClass: 'message-item',
            appendHtml: function(cv, iv, index) {
                var post = index,
                    $existingItems = cv.$('li.message-item'),
                    index = (index) ? index : $existingItems.length,
                    position = $existingItems.eq(index - 1),
                    $itemView = $(iv.el);
                if (!post || position.length === 0) {
                    cv.$el.prepend(iv.el);
                } else {
                    $itemView.insertAfter(position);
                }
            }
        });

        Views.ReportPlaces = Backbone.View.extend({
            events: {
                'submit form.form-report': 'submitAttach'
            },
            initialize: function(options) {
                var view = this;
                view.blockUi = new Views.BlockUi();
                if ($('.report-container').find('.postdata').length > 0) {
                    var postsdata = JSON.parse($('.report-container').find('.postdata').html());
                    view.messages = new Collections.MessagesChatBox(postsdata);
                } else {
                    view.messages = new Collections.MessagesChatBox();
                }
                /**
                 * init list blog view
                 */
                this.ListMsg = new ListMessageChatBox({
                    itemView: ReportItem,
                    collection: view.messages,
                    el: $('.report-container').find('.list-chat-work-place')
                });
                /**
                 * init block control list blog
                 */
                new Views.BlockControl({
                    collection: view.messages,
                    el: $('.report-container')
                });

                // init upload file control
                this.docs_uploader = {};
                this.filecontroller = new Views.FileUploaderChatBox({
                    el: $('#report_docs_container'),
                    uploaderID: 'report_docs',
                    fileIDs : []
                });
                this.docs_uploader = this.filecontroller.docs_uploader;

            },
            submitAttach: function(e) {
                var self = this;
                var uploaded = false,
                    $target = $(e.currentTarget);
                e.preventDefault();
                if (this.docs_uploader.controller.files.length > 0) {
                    this.docs_uploader.controller.bind('StateChanged', function(up) {
                        if (up.files.length === up.total.uploaded) {
                            // if no errors, post the form
                            if (!self.hasUploadError && !uploaded) {
                                self.sendMessage($target);
                                uploaded = true;
                            }
                        }
                    });
                    this.hasUploadError = false; // reset the flag before re-upload
                    this.docs_uploader.controller.start();
                } else {
                    this.sendMessage($target);
                }
            },
            sendMessage: function(target) {
                var message = new Models.Report(),
                    view = this,
                    $target = target;
                $target.find('textarea, input, select').each(function() {
                    message.set($(this).attr('name'), $(this).val());
                });
                message.set('fileID' , this.filecontroller.fileIDs);
                this.filecontroller.fileIDs = [];
                message.save('', '', {
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(result, res, xhr) {
                        view.blockUi.unblock();
                        view.$('textarea').val('');
                        view.docs_uploader.controller.splice();
                        view.docs_uploader.controller.refresh();
                        if (res.success) {
                            view.messages.add(message);
                            view.ListMsg.render();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            }
        });
        new Views.ReportPlaces({
            el: 'div.report-details'
        });
    })
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);