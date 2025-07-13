/**
 * jQuery jWin plugin
 * @name jwin.js
 * @author Maxim Antonov http://www.maxantonov.name/
 * @version 0.1
 * $Revision: 507 $
 * $Author: idler $
 * $Date: 2009-04-13 18:38:19 +0400 (Пнд, 13 Апр 2009) $
 * @category jQuery plugin
 * @copyright (c) 2008 Maxim Antonov (www.maxantonov.name)
 * @license GPL
 */

/**
 * USAGE:
 * var w = $.jWin.create(options);
 *
 * OPTIONS:
 * autoShow (bool): show window automaticaly after it created default false
 * modal (bool):    is window modal default false
 * overlayOpacity (float): opacity of overlay - can be used, only if window is modal default 0.7
 * overlayColor (string):  css-color of overlay layer default black
 * title (string): title
 * content (string): content
 * width (int): default 500
 * height (int): default 200
 * alwaysCentered (bool): default false
 */


(function($){

  $.jWin = function(options){
  var defaults = {
    autoShow: true,
    modal: true,
    zAxis: 500,
    overlayOpacity: 0.98,
    openDuration: 900
  };
  
  var options = $.extend(defaults, options);

  $.extend($.jWin, options);
  var ind=1;
  }
  /**
   * the Handle to keep concrete window pointer
   */
  $.jWin.handle = 0;
  $.jWin.zAxis = 50;
  $.jWin.handleDB = [];
  $.jWin.version = '0.1';

  /**
   * create a new window
   */
  $.jWin.create = function(options){
    return new jWindow(options||j);
  }

  /**
   * Window constructor
   * @param options config object
   */
  var jWindow = function(options){
    //console.dir(options);
    var defaults = {
       alwaysCentered:false,
       autoShow:false,
       modal:false,
       overlayOpacity: 0.7,
       overlayColor: '#000000',
       title: 'www.maxantonov.name',
       content: '<p>Sed ut <strong>perspiciatis</strong>, ' +
       'unde omnis iste natus error sit voluptatem accusantium doloremque' +
       'laudantium, totam rem aperiam eaque ipsa,' +
       'quae ab illo inventore veritatis et quasi ' +
       'architecto beatae vitae dicta sunt, explicabo.</p>',
       width: 500,
       height: 200,
       handleEscape:false
    };

    /// add options in defaults with overwriting and return changed defaults
    this.config = $.extend(defaults, options);

    this.init();
  }

  /**
   * initialize new window
   */
  jWindow.prototype.init = function(){
    //console.log('init');
    $.jWin.handle++;
    this.handle = $.jWin.handle;
    $.jWin.handleDB[this.handle] = this;

    this.create();

   // console.dir(this);
   // console.dir($.jWin.handleDB);
  }

  // Common properties
  jWindow.prototype.handle = 0;
  jWindow.prototype.dom = {};
  jWindow.prototype.title = {};
  jWindow.prototype.body = {};
  jWindow.prototype.overlay = false;
  jWindow.prototype.header = {};


  $.extend(jWindow.prototype,{

    /**
     * creating domElement of this window
     */
    create: function(){
        this.onBeforeCreate();
        this.overlay = this.createOverlay();

        this.dom = $('<div class="jw-wrap" id="jw'+this.handle+'">'+
            '<div class="jw-head" id="jwhead' + this.handle + '">'+
            '<div class="jw-closer">закрыть</div><div class="jw-title"></div>' +

            '</div>' +
            '<div class="jw-body">' +
                '<div class="jw-content"></div>'+
            '</div></div>'
        );
        this.dom.appendTo('body');
        $.jWin.zAxis+=10;
        this.calculatePosition();
        this.dom.css({
                        position: 'absolute',
                        visibility:'visible',
                        display:'none',
                        'z-index':$.jWin.zAxis
                      });




        this.setTitle(this.config.title);
        this.setContent(this.config.content);

        this.calculatePosition();

        if(this.config.alwaysCentered){
            var slf = this;
            $(window).bind('resize',function(){slf.calculatePosition();});
            $(window).bind('scroll',function(){slf.calculatePosition();});
        }

      if(this.config.autoShow)  this.show();
    },
    setTitle: function(title){
       this.dom.find('.jw-title').html(title);
    },
    setContent: function(content){
       this.dom.find('.jw-content').html(content);
    },

    onBeforeCreate:   function(){},
    createOverlay:    function(){

      if(!this.config.modal) return false;
      var html = "<div class=\"overlay\"></div>";
      var z=$.jWin.zAxis -1;
      var h = $(document).height(); var w = $(document).width();
      var   o = $(html).appendTo('body');
      o.css({
             position:'absolute',
             top:0,left:0,
             opacity:this.config.overlayOpacity,
             backgroundColor:this.config.overlayColor,
             visibility:'visible',
             display:'none',
             'z-index':z,
             height:h,width:w
            });
                    $(window).bind('resize',function(){
                            var h = $(document).height();
                            var w = $(document).width();
                            o.css({height:h,width:w});
                    });
        return o;
    },

    show: function(){

        this.calculatePosition();

        if(this.overlay){
            var o = $(this.overlay);
            o.fadeIn(this.config.openDuration);
        }

        this.calculatePosition();
        this.dom.fadeIn(this.config.openDuration);
        var self = this;

        this.dom.find('.jw-closer').click( function(){self.close();});
        var self=this;
        if(this.config.handleEscape){
          $(document).bind('keypress', {handle:self}, self.escapeClose);
        }

    },

    calculatePosition: function(){

       this.dom.css({width:this.config.width});
       var dh = this.dom.find('.jw-body').height();
       //if(dh>this.config.height){
            this.dom.find('.jw-content').css({height:this.config.height,overflow:'auto'}); // auto | hidden | scroll | visible
       //}

       var wnd = $(window);
       var doc = $(document);
       var pTop = doc.scrollTop();
       var pLeft = doc.scrollLeft(),
                minTop = pTop;
            pTop += (wnd.height() - this.dom.height()) / 2;
            pTop = Math.max(pTop, minTop);
            pLeft += (wnd.width() - this.dom.width()) / 2;
            this.dom.css({top: pTop, left: pLeft});

    },
    close: function(){
       var self = this;
       $(document).unbind('keypress', self.escapeClose);
       
      if(this.config.onBeforeClose){
        this.onBeforeClose = this.config.onBeforeClose;
      }
      this.onBeforeClose();
      var self = this;
     // console.log('closing',this.dom);
      var dom = this.dom;
      dom.fadeOut(300,function(){self.onAfterClose();dom.remove();} );
      var o = $(this.overlay);
      o.fadeOut(500,function(){o.remove();});

       //console.log('closed');
    },
    onBeforeClose: function(){},
    onAfterClose: function(){},
    escapeClose: function(e){
          if(e.keyCode == 27) e.data.handle.close();
    }
  });

})(jQuery);
/*
var i = {x:1,y:2,z:4};$.jWin.revision= (("$Revision: 507 $").replace(/^.*([0-9]*).*$/,'\$1'));
var s = {x:7,s:33,z:8};
var q = $.extend(i,s);
console.dir([i,s,q]);
*/
