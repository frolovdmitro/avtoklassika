/**
 * miniSet jQuery plugin
 *
 * The plugin may be used to display:
 * a simple Lightbox for an image
 * a Slider with multiple images
 * an image Gallery
 *
 *
 * @author Julius Semenas
 */
$(document).ready(function(){
    'use strict';

    (function($) {

        // function for displaying the lightbox with overlay
        $.fn.showLightbox = function() {
            var windowHeight = window.innerHeight || $(window).height(),
            windowWidth  = window.innerWidth  || $(window).width();

            // Display the overlay
            $('<div id="ms-overlay"></div>')
            .css({'opacity' : '0.9'})
            .appendTo('body');

            // Create the lightbox container
            $('<div id="ms-lightbox"></div>')
            .hide()
            .appendTo('body');

            // Display the image on load
            $('<img>')
            .attr('src', $(this).attr('src'))
            .css({
                'max-height': windowHeight * 0.75, 
                'max-width':  windowWidth * 0.75
            })
            .load(function() {
                $('#ms-lightbox')
                .css({
                    'top':  (windowHeight - $('#ms-lightbox').height()) / 2,
                    'left': (windowWidth - $('#ms-lightbox').width())  / 2,
                })
                .fadeIn();
            })
            .appendTo('#ms-lightbox');

            // Display the title of the image
            $('<div id="ms-title-wrap"></div>')
            .appendTo('#ms-lightbox');

            $('<p id="ms-title"></p>')
            .appendTo('#ms-title-wrap')
            document.getElementById("ms-title").innerHTML = $(this).attr('title');

            // Remove Lightbox via click
            $('#ms-overlay, #ms-lightbox').click(function() {
                $('#ms-overlay, #ms-lightbox').remove();
            });

            // Remove Lightbox via ESC press
            $(document).keyup(function(event) {
                if ( event.which == 27 ) {
                    $('#ms-overlay, #ms-lightbox').remove();
                }
            });

            return this;
        };

        /**
        * Slideshow function
        *
        * @param array params with all Slider parameters to be replaced with default ones
        */
        $.fn.showSlider = function(params) {

            // extend default parameters with passed ones
            var options = $.extend({
                'height': 300,
                'fadeTime': 500,
                'intervalTime': 5000,
                'tray': true
            }, params),

            imgCount = $(this).children().length, // number of images
            trayMenu = $('<ul id="tray"></ul>'), // ul for the tray under imageItem
            dothtml = '<li class="dot selected"><a href="#">0</a></li>', // first li in the #tray ul
            imageItem = $(this).children(), // objects of all li elements for the slider ul,
            pics = imageItem.children(), // all images
            tray, // the tray object
            intervalID, // the interval
            intervalTime  = options.intervalTime, // time between executions
            fade          = options.fadeTime, // picutre fade time
            height        = options.height; // Slider height

            // Set max height of #ms-slider and images
            $(this).css({'height': height + 'px'});
            pics.css({'height': height + 'px'});


            if(options.tray) {
                // make one list item for each image
                for(var i=1;i<imgCount;i++) {
                    dothtml += '<li class="dot deselected"><a href="#">' + i + '</a></li>';
                }

                // create the tray menu
                trayMenu.append(dothtml);
                trayMenu.insertAfter($(this));

                tray = $('#tray').children(); // tray items

                // upon clicking one of the tray items
                tray.click(function(event) {

                    event.preventDefault(); // prevent default event

                    var chosenPicIndex = $(this).text(); // get value of clicked item

                    clearInterval(intervalID); // clear the interval

                    // swapt images by fading out and then in
                    fadeOutAndIn(imageItem.eq(chosenPicIndex));

                    // restart the interval after fade animation is complete
                    setTimeout(function() {
                        makeInterval();
                    }, fade);
                });

            }
            // give all pictures z-index 1 and opacity 1
            imageItem.css({'zIndex': '1', 'opacity': '1'});

            // add class .visible to first image
            imageItem.eq(0).addClass('visible').css({'zIndex': '3', 'position': 'relative'});

            /**
             * Function to change focus from one dot to the next
             *
             * @param object currDot for current dot
             * @param object nextDot for the next dot in line
             *
             */
            function changeDotFocus(currentDot, nextDot) {
              currentDot
                .removeClass('selected')
                .addClass('deselected');
              nextDot
                .removeClass('deselected')
                .addClass('selected');
            }

            /**
             * Function to swap the current and next (or chosen) picture by fading them in and out
             *
             * @param object chosenPic for the chosen picture
             *
             */
            function fadeOutAndIn(chosenPic) {

              var current = $('.visible'),
                  next;
              
              // if parameter is empty, get index of next picture
              if(!chosenPic) {

                // if the current picture is the last in list, set index of the first picture
                if(current.index() === imgCount -1) {
                  next = 0;
                }
                // set the next pciture
                else {
                  next = current.index() + 1;
                }
                
                chosenPic = imageItem.eq(next);
              }
              
              // swap imaes only if the select picture is different
              if(current.index() !== chosenPic.index()) {
              
                // make sure that the next picture is under the current one in stack
                chosenPic.css({'zIndex': '2'});
                
                if(options.tray) {
                  // change the selected dot
                  changeDotFocus(tray.eq(current.index()), tray.eq(chosenPic.index()));
                }
                
                // fade out the current picture
                current.fadeTo(fade, 0.0, function() {
                
                  // hide current picture
                  current
                    .removeClass('visible')
                    .css({'zIndex': '1', 'opacity': '1'});
                  
                  // display the chose (or next) image
                  chosenPic
                    .addClass('visible')
                    .css({'zIndex': '3'});
                });
              }
            }

            /**
             * Function to set the interval execution
             *
             */
            function makeInterval() {
              intervalID = setInterval(function() {
                fadeOutAndIn();
              }, intervalTime);
            }

            // initiate interval execution
            makeInterval();

            return this;
        };


        /**
         * Gallery function for displaying a list of pictures in a gallery
         *
         * @param array params with all Gallery parameters to be replaced with default ones
         */
        $.fn.showGallery = function(params) {
          
          // set Gallery classes to all gallery items
          $('#ms-gallery').children().addClass('ms-gallery-item');

          // extend default parameters with passed ones
          var options = $.extend({
            'imgCounter': true,
            'width': 165,
            'height': 95
          }, params),

          picWidth = options.width, // width of the pictures
          picHeight = options.height, // height of the pictures
          listItems = $(this).children(), // all the li children
          imageItem = listItems.children(), // all the pictures belonging to the listItems
          imgCount = listItems.size(); // total imgCount of listItems

          // set the width and height of the pictures
          imageItem.css({'width': picWidth + 'px', 'height': picHeight + 'px'});

          // bind a click event to gallery items
          imageItem.click(function() {
            // index of the clicked item
            var imageId = $(this).parent().index();
            
            // load the selected picuture
            loadImg($(this), imageId);
          });


          /**
           * Function to display the selected picture
           *
           * @param object obj of the clicked item
           * @param integer imageId for the index of the clicked item
           *
           */
          function loadImg(obj, imageId) {
          
            $('#ms-lightbox, #ms-overlay').remove();
          
            // show the lLightbox
            obj.showLightbox();
            
            // show navigation arraows
            createArrowLinks(imageId);
          }
          
          /**
           * Function to create the navigation arraws
           *
           * @param integer imageId for the index of the shown picture
           */
          function createArrowLinks(imageId) {
          
            // establish picture placement coordinates
            var top = ($(window).height() - $('#ms-lightbox').height()) / 2,
            side = ($(window).width() - $('#ms-lightbox').width()) / 2,
            middle = $('#ms-lightbox').height() / 2,
            
            prevImg, // previous picture
            nextImg; // next picture
            
            // create left arrow if not the first item in the list
            if(imageId !== 0) {
            
              // previous image object
              prevImg = $(listItems).eq(imageId - 1).children();
            
              // create the arrow
              $('<div id="arrow-left"></div>')
              .appendTo('#ms-lightbox')
              .css({left: '-80px', top: middle})
              .click(function() {
                loadImg(prevImg, imageId - 1);
              });

              $('<div id="arrow-left-icon"></div>')
              .appendTo('#arrow-left');
            }
            
            // create right arrow if not the last item in the list
            if(imageId + 1 !== imgCount) {
            
              // next image object
              nextImg = $(listItems).eq(imageId + 1).children();
            
              // create the arrow
              $('<div id="arrow-right"></div>')
              .appendTo('#ms-lightbox')
              .css({right: '-80px', top: middle})
              .click(function() {
                loadImg(nextImg, imageId + 1);
              });

              $('<div id="arrow-right-icon"></div>')
              .appendTo('#arrow-right');
            }

            // display the image counter if requested
            if(options.imgCounter) {
              imgCounter(imageId, top, side);
            }
          
          }
          
          /**
           * Function to show the picture counter within the gallery
           *
           * @param integer imageId, number of picture currently being shown
           *
           */
          function imgCounter(imageId) {
          
            // append the counter container
            $('<div id="counter"></div>')
            .appendTo('#ms-lightbox');

            // append the counter text
            $('<span></span>')
            .appendTo('#counter')
            .text((imageId + 1) + '/' + imgCount);
            
          }
        
        return this;

        };
      
    }) (jQuery);

    // bind the click event for displaying the lighbox with overlay
    $('.ms-lightbox').click(function() {
        $(this).showLightbox(); 
    });
  
});