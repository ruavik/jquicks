jq.newClass('CFLoader','CVidget',{
    /*===Реагирует на события:===
===Генерирует события:===
*/
    construct:function(){
        jq.CFLoader.superclass.constructor.apply(this,arguments);
        this.queue = [];
        this.num_current_uploads = 0;
        this.selected_files = [];
        this.selected_files.len = 0;
        
        jq.registerEventHandler(this.view_model,"onfetch",[this,'redraw']);
    },
    onload:function (){
        jq.CFLoader.superclass.onload.call(this);
        
    },
    handle_files: function  (file_list) {
        for ( var i=0; i<file_list.length; i++ ){
            var file = file_list[i];
            // Too large
            if ( this.max_size_MB && (file.size / 1024 / 1024) > this.max_size_MB ) {
                alert(file.name + ' is too large; maximum is ' + this.max_size_MB.toFixed(2) + ' MB');
            } else {
                //file.label = add_label(file);
                // Enqueue it
                if ( this.max_concurrent > -1 && this.num_current_uploads >= this.max_concurrent )
                    this.queue.push(file);
                // Upload it
                else this.process_file(file);
            }
        }
    },
    process_file:function  (file) {
        this.num_current_uploads += 1;
        var this_obj = this;
        var reader = new FileReader();
        reader.onload = function(e) {
            var file_contents = e.target.result.split(',')[1];
            this_obj.upload_file(file, file_contents);
        }
        reader.readAsDataURL(file);
    },

    upload_file:function (file, file_contents) {
        jq.get(this.view_model).insert({
            origin:file.name,
            filesize:file.size,
            filetype:file.type,
            data:file_contents
        }); 
        this.num_current_uploads -= 1;
        if (this.queue.length > 0 ) process_file(this.queue.shift())
    // Remove the label in 1 second
    //setTimeout(function() { uploads.removeChild(file.label) }, 1000);
    },
    change_selection:function (act,idx){
        var delete_button = document.getElementById(this.name+'_delete_files');
        if(act){
            this.selected_files[idx]=1;
            this.selected_files.len +=1; 
        }
        else{
            delete this.selected_files[idx];
            this.selected_files.len -=1;
        }
        if (this.selected_files.len>0){
            delete_button.disabled = false;
        }else{
            delete_button.disabled = true;
        }
    },
    delete_selection:function (){
        if (this.selected_files.len>0){
            if (confirm('Действительно удалить выбранные файлы?')) {
                for (var i in this.selected_files) {
                    if(i != 'len') {
                        jq.get(this.view_model).remove(i,false);
                    }
                }
                jq.get(this.view_model).fetch();
            }
        }
    },
    log_it:function (text){
        document.getElementById(this.name+'_log').innerHTML = text;
    }
    
});