window.nForumMap = {
    _load:false,
    map:null,
    markers:[],
    geocoder:null,
    loadJs:function(name, opt){
        var url = 'https://maps.google.com/maps/api/js?sensor=false&region=GB&key=' + window.SYS.GAPIKEY + '&callback=' + name;
        $.getScript(url);
        this.opt = opt;
    },
    parseMap:function(){
        var option = {
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            disableDoubleClickZoom:true
        };
        $('.map-map').each(function(){
            var bound,marker;
            try{
                bound = eval('(['+$(this).attr('_bound').replace(/{/g,'[').replace(/}/g,']')+'])');
                marker = eval('(['+$(this).attr('_mark').replace(/{/g,'[').replace(/}/g,']')+'])');
            }catch(e){
                return;
            }
            bound = new google.maps.LatLngBounds(new google.maps.LatLng(bound[0][0],bound[0][1]),new google.maps.LatLng(bound[1][0],bound[1][1]));
            option.center = bound.getCenter();
            var map = new google.maps.Map(this, option);
            map.fitBounds(bound);
            for(var i in marker){
                new google.maps.Marker({
                    position: new google.maps.LatLng(marker[i][0],marker[i][1]),
                    map: map
                });
            }
        });
    },
    init:function(){
        if(this._load)
            return;
        this._load = true;
        this.option = {
            x:39.9042140,
            y:116.4074130,
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            disableDoubleClickZoom:true
        };
        this.option.center = new google.maps.LatLng(this.option.x, this.option.y);
        this.map = new google.maps.Map(document.getElementById(this.opt.area), this.option);
        google.maps.event.addListener(this.map, 'click', function(event) {
            nForumMap.addMarker(event.latLng);
        });
        this.geocoder = new google.maps.Geocoder();
        $('#map_search').click(function(){
            nForumMap.search($('#map_txt').val());
        });
        $('#map_insert_btn').click(this.opt.onInsert);
    },
    addMarker:function(lo){
        var marker = new google.maps.Marker({
            position: lo,
            map: this.map,
            draggable:true
        });
        google.maps.event.addListenerOnce(marker, 'click', function(event){
            marker.setMap(null);
            var len = nForumMap.markers.length;
            for(var i=0;i<len;i++){
                if(marker == nForumMap.markers[i]){
                    nForumMap.markers.splice(i, 1);
                }
            }

        });
        this.markers.push(marker);
    },
    search:function(address){
       if (this.geocoder) {
           this.geocoder.geocode( { 'address': address,'latLng':this.option.center}, function(results, status) {
               if (status == google.maps.GeocoderStatus.OK) {
                   var lo = results[0].geometry.location;
                   nForumMap.map.setCenter(lo);
                   nForumMap.map.fitBounds(results[0].geometry.viewport);
               } else {
                   alert("���ź��޷���λ���κν������ʹ�÷�Χ�ϴ�Ĺؼ��ʽ��ж�λ����:�����к�����xx·");
               }
           });
       }
   }

};
(jQuery.fn.extend({
    ubb:function(config){
         var ubb_config = {
             enable : true,
             ubb_face : ['����', '����_GB2312', '������', '����', '����', 'Andale Mono', 'Arial', 'Arial Black', 'Courier New', 'Tahoma', 'Times New Roman', 'Verdana', 'Lucida Console'],
             ubb_size : ["1", "2", "3", "4", "5", "6", "7", "8", "9"],
             ubb_color :["#FAEBD7","#00FFFF","#7FFFD4","#F0FFFF","#F5F5DC","#FFE4C4","#000000","#FFEBCD","#0000FF","#8A2BE2","#A52A2A","#DEB887","#5F9EA0","#7FFF00","#D2691E","#FF7F50","#6495ED","#FFF8DC","#DC143C","#00FFFF","#00008B","#008B8B","#B8860B","#A9A9A9","#006400","#BDB76B","#8B008B","#556B2F","#FF8C00","#9932CC","#8B0000","#E9967A","#8FBC8F","#483D8B","#2F4F4F","#00CED1","#9400D3","#FF1493","#00BFFF","#696969","#1E90FF","#B22222","#FFFAF0","#228B22","#FF00FF","#DCDCDC","#F8F8FF","#FFD700","#DAA520","#808080","#008000","#ADFF2F","#F0FFF0","#FF69B4","#CD5C5C","#4B0082","#FFFFF0","#F0E68C","#E6E6FA","#FFF0F5","#7CFC00","#FFFACD","#ADD8E6","#F08080","#E0FFFF","#FAFAD2","#90EE90","#D3D3D3","#FFB6C1","#FFA07A","#20B2AA","#87CEFA","#778899","#B0C4DE","#FFFFE0","#00FF00","#32CD32","#FAF0E6","#FF00FF","#800000","#66CDAA","#0000CD","#BA55D3","#9370DB","#3CB371","#7B68EE","#00FA9A","#48D1CC","#C71585","#191970","#F5FFFA","#FFE4E1","#FFE4B5","#FFDEAD","#000080","#FDF5E6","#808000","#6B8E23","#FFA500","#FF4500","#DA70D6","#EEE8AA","#98FB98","#AFEEEE","#DB7093","#FFEFD5","#FFDAB9","#CD853F","#FFC0CB","#DDA0DD","#B0E0E6","#800080","#FF0000","#BC8F8F","#4169E1","#8B4513","#FA8072","#F4A460","#2E8B57","#FFF5EE","#A0522D","#C0C0C0","#87CEEB","#6A5ACD","#708090","#FFFAFA","#00FF7F","#4682B4","#D2B48C","#008080","#D8BFD8","#FF6347","#40E0D0","#EE82EE","#F5DEB3","#FFFFFF","#F5F5F5","#FFFF00","#9ACD32"],

             ubb_img : [{id:'ubb_bold', alt:'�Ӵ�', src:'bold.gif'},
                        {id:'ubb_italic', alt:'б��', src:'italic.gif'},
                        {id:'ubb_underline', alt:'�»���', src:'underline.gif'},
                        {id:'ubb_url', alt:'����', src:'url.gif'},
                        {id:'ubb_mail', alt:'�ʼ�', src:'mail.gif'},
                        {id:'ubb_img', alt:'ͼƬ', src:'img.gif'},
                        {id:'ubb_swf', alt:'Flash', src:'flash.gif'},
                        {id:'ubb_mp3', alt:'��Ƶ', src:'music.gif'},
                        {id:'ubb_map', alt:'��ͼ', src:'map.gif'}],
             ubb_img_path:"",
             ubb_em : null,
             ubb_em_img : [{name:'����', path:'em', start:1, num:74},
                             {name:'������', path:'ema', start:0, num:42},
                             {name:'��˹��', path:'emb', start:0, num:25},
                             {name:'���ͷ', path:'emc', start:0, num:59}],
             ubb_syntax_enable: false,
             ubb_syntax: [{name:'ActionScript3', alias:'as3'},
                             {name:'Bash/shell', alias:'bash'},
                             {name:'ColdFusion', alias:'cf'},
                             {name:'C/C++', alias:'c'},
                             {name:'C#', alias:'csharp'},
                             {name:'CSS', alias:'css'},
                             {name:'Delphi', alias:'delphi'},
                             {name:'Diff', alias:'diff'},
                             {name:'Erlang', alias:'erl'},
                             {name:'Groovy', alias:'groovy'},
                             {name:'Java', alias:'java'},
                             {name:'JavaFx', alias:'jfx'},
                             {name:'JavaScript', alias:'js'},
                             {name:'Perl', alias:'pl'},
                             {name:'PHP', alias:'php'},
                             {name:'Text', alias:'text'},
                             {name:'Python', alias:'py'},
                             {name:'Ruby', alias:'rb'},
                             {name:'Scala', alias:'scala'},
                             {name:'SQL', alias:'sql'},
                             {name:'Visual Basic', alias:'vb'},
                             {name:'XML/HTML', alias:'xml'}]
         };

        config = $.extend(ubb_config, config);
        var textarea = this;
        textarea._getSelection = function(){
            var ret = "";
            var doc = this.get(0).document;
            if(doc){
                var sel = doc.selection.createRange();
                if (sel.text.length > 0)
                    ret = sel.text;
            }else{
                var start = this.get(0).selectionStart,
                    end = this.get(0).selectionEnd;
                if(start != end)
                    ret = this.get(0).value.substring(start, end);
            }
            return $.trim(ret);
        };

        textarea._makeUBB = function(template, valid){
            var txt = this._getSelection();
            if(txt == "" && valid){
                alert('����ѡ������');
                return false;
            }
            var doc = this.get(0).document;
            var t = template.replace(/%content%/, txt);
            if (doc) {
                this.get(0).focus();
                var range = doc.selection.createRange();
                if(range.text == ""){
                    this.get(0).value += t;
                }else{
                    range.text = t;
                    this.get(0).focus();
                    range.collapse();
                }
            }else{
                var s = this.get(0).selectionStart,
                e = this.get(0).selectionEnd,
                val = this.get(0).value;
                this.get(0).value = val.substring(0,s) + t + val.substring(e);
            }
            this.focus();
        };
        if(config.enable){
            var wrap = document.createElement('div');
            $(wrap).attr("id", "ubb_wrap");
            this.wrap(wrap);
            $('#ubb_wrap').prepend((function(){
                var ret = "";
                ret += '����&ensp;<select id="ubb_face">';
                ret += '<option value="">��ѡ��</option>';
                for(var i in config.ubb_face){
                    ret += '<option value="' + config.ubb_face[i] + '">' +config.ubb_face[i] + '</option>';
                }
                ret += '</select>&emsp;';
                ret += '�ֺ�&ensp;<select id="ubb_size" >';
                ret += '<option value="">��ѡ��</option>';
                for(var i in config.ubb_size){
                    ret += '<option value="' + config.ubb_size[i] + '">' +config.ubb_size[i] + '</option>';
                }
                ret += '</select>&emsp;';
                ret += '��ɫ&ensp;<select id="ubb_color" >';
                ret += '<option value="">��ѡ��</option>';
                for(var i in config.ubb_color){
                    ret += '<option style="background-color:' +config.ubb_color[i] + ';color:' +config.ubb_color[i] + '"value="' + config.ubb_color[i] + '">' +config.ubb_color[i] + '</option>';
                }
                ret += '</select>&emsp;';
                if(config.ubb_syntax_enable){
                    ret += '�﷨����&ensp;<select id="ubb_syntax" >';
                    ret += '<option value="">��ѡ��</option>';
                    for(var i in config.ubb_syntax){
                        ret += '<option value="' + config.ubb_syntax[i]['alias'] + '">' +config.ubb_syntax[i]['name'] + '</option>';
                    }
                    ret += '</select>&emsp;';
                }
                ret += '<br /><div class="ubb-icon">';
                for(var i in config.ubb_img){
                    ret += ('<img id="' + config.ubb_img[i].id + '" src="' + config.ubb_img_path + config.ubb_img[i].src + '" alt="' + config.ubb_img[i].alt + '" title="' + config.ubb_img[i].alt + '"border="0">');
                }
                return ret + '</div>';
            })());

            $('#ubb_color').change(function(){$(this).val() && textarea._makeUBB("[color=" + $(this).val()+ "]%content%[/color]", true);});
            $('#ubb_size').change(function(){$(this).val() && textarea._makeUBB("[size=" + $(this).val()+ "]%content%[/size]", true);});
            $('#ubb_face').change(function(){$(this).val() && textarea._makeUBB("[face=" + $(this).val()+ "]%content%[/face]", true);});
            $('#ubb_bold').click(function(){textarea._makeUBB("[b]%content%[/b]", true);});
            $('#ubb_italic').click(function(){textarea._makeUBB("[i]%content%[/i]", true);});
            $('#ubb_underline').click(function(){textarea._makeUBB("[u]%content%[/u]", true);});
            $('#ubb_syntax').change(function(){$(this).val() && textarea._makeUBB("[code=" + $(this).val()+ "]\n%content%\n[/code]", true);});

            $('#ubb_url').click(function(e){
                if(textarea._getSelection()== ""){
                    alert('����ѡ������');
                    return false;
                }
                var url = prompt("������������ַ\n����http|https|ftp|rtsp|mms��ͷ", "http://");
                var reg = /^http|https|ftp|rtsp|mms:\/\//i;
                if(url == null)
                    return false;
                if(!reg.test(url)){
                    alert('�Ƿ������ӵ�ַ');
                    return false;
                }
                textarea._makeUBB("[url=" + url + "]%content%[/url]", false);
            });
            $('#ubb_mail').click(function(e){
                if(textarea._getSelection()== ""){
                    alert('����ѡ������');
                    return false;
                }
                var mail = prompt('����������ʼ���ַ', 'XXX@domain');
                var reg  = /^([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/gi;
                if(mail == null)
                    return false;
                if(!reg.test(mail)){
                    alert('�Ƿ����ʼ���ַ');
                    return false;
                }
                textarea._makeUBB("[email=" + mail + "]%content%[/email]", false);
            });
            $('#ubb_img').click(function(e){
                var img = prompt('������ͼƬ��ַ', 'http://');
                var reg = /^http:\/\//i;
                if(img == null)
                    return false;
                if(!reg.test(img)){
                    alert('�Ƿ���ͼƬ��ַ');
                    return false;
                }
                textarea._makeUBB("[img=" + img + "]%content%[/img]", false);
            });
            $('#ubb_swf').click(function(e){
                var swf = prompt("������Flash��ַ", "http://");
                var reg = /^http:\/\//i;
                if(swf == null)
                    return false;
                if(!reg.test(swf)){
                    alert('�Ƿ���Flash��ַ');
                    return false;
                }
                textarea._makeUBB("[swf=" + swf + "]%content%[/swf]", false);
            });
            $('#ubb_mp3').click(function(e){
                var mp3 = prompt('��������Ƶ��ַ', 'http://');
                var reg = /^http:\/\//i;
                if(mp3 == null)
                    return false;
                if(!reg.test(mp3)){
                    alert('�Ƿ�����Ƶ��ַ');
                    return false;
                }
                textarea._makeUBB("[mp3=" + mp3 + " auto=0]%content%[/mp3]", false);
            });

            $('#ubb_map').click(function(e){
                if($('#map_area').length == 0){
                    $('<div id="map_area"><div id="map_canvas"></div><div id="map_func"><label>����һ����Χ�ؼ��ʶ�λ&nbsp;&nbsp;</label><input class="input-text" id="map_txt" type="textbox" value="" />&nbsp;&nbsp;<input class="submit" id="map_search" type="button" value="��λ" /><div id="map_insert"><label>������ͼ���һ�����ƶ���ǣ��������ɾ��</label>&nbsp;&nbsp;<input class="submit" id="map_insert_btn" type="button" value="�������б��" /></div></div></div>').appendTo($('#body'));
                    nForumMap.loadJs('nForumMap.init',{'area':'map_canvas','onInsert':function(){
                        var m = nForumMap.markers,out=[],bound=nForumMap.map.getBounds(),b=[];
                        b.push('{'+bound.getSouthWest().lat()+','+bound.getSouthWest().lng()+'}');
                        b.push('{'+bound.getNorthEast().lat()+','+bound.getNorthEast().lng()+'}');
                        for(var i in m){
                            out.push('{'+m[i].getPosition().lat()+','+m[i].getPosition().lng()+'}');
                        }
                        if(out.length > 0){
                            textarea._makeUBB("[map="+b.join(',')+" mark=" + out.join(',') + "][/map]", false);
                            $('#map_area').dialog('close');
                        }else
                            alert('���������һ�����');
                    }});
                    $('#map_area').dialog({
                        modal:true,
                        resizable:false,
                        autoOpen:true,
                        title:"��ͼ",
                        width:600,
                        zIndex:2,
                        autoOpen:false,
                        bgiframe:true
                    });
                }
                $('#map_area').dialog('open');
            });
        }

        if(config.ubb_em && config.ubb_em instanceof jQuery){
            var em = config.ubb_em,
            file = config.ubb_em_img,
            tab = $('<div class="ubb-img-tab"></div>'),
            img = $('<div class="ubb-img"></div>'),
            li = '';
            for(var i in file){
                li += ('<li _index="' + i + '" _cur="0" ><div>' + file[i].name + '</div></li>');
            }
            tab.append(li);
            em.append(tab);
            em.append(img);
            function _img_update(n){
                var tmp = '',
                path = file[n].path,
                start = file[n].start,
                num = file[n].num;
                for(var i=start; i<num;i++){
                    tmp += ('<img src="'+ config.ubb_img_path + path + '/' + i + '.gif" _val="' + i + '"/>');
                }
                img.empty().append(tmp);
            }
            em.on('click','li',function(){
                var _cur = $(this).attr('_cur');
                if(_cur == "1"){
                    em.find('.ubb-img').toggle();
                    return false;
                }
                em.find('.ubb-img').show();
                tab.find("li").not($(this)).attr('_cur', "0").find('div').removeClass();
                $(this).attr('_cur', "1").find('div').addClass('selected');
                _img_update($(this).attr('_index'));
            }).on("click", 'img', function(){
                if(($.isIE(7) || $.isIE(8)) && textarea.get(0).value == textarea.attr('placeholder')) textarea.get(0).value = '';
                textarea._makeUBB('[' + file[tab.find('li[_cur="1"]').attr('_index')].path + $(this).attr('_val') + ']', false)
            });
            //tab.find('li').eq(0).click();
        }
        return $(this);
    }
 }));
