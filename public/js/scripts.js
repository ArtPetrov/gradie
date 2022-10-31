(function($) {
  $(function() {
    $('button.header__nav-toggle').click(function() {
      var headerNav = $('nav.header-nav');
      if (headerNav.is('.header-nav--active')) {
        headerNav.removeClass('header-nav--active');
      } else {
        headerNav.addClass('header-nav--active');
      }
      $('button.header-nav__close').click(function() {
        headerNav.removeClass('header-nav--active');
      });
    });

    $('button.header-nav__toggle').click(function() {
      var headerNavToggle = $(this);
      var headerNavSubMenu = headerNavToggle.next('ul.header-nav__sub-menu');
      if (headerNavSubMenu.is('.header-nav__sub-menu--active')) {
        headerNavSubMenu.removeClass('header-nav__sub-menu--active');
      } else {
        headerNavSubMenu.addClass('header-nav__sub-menu--active');
      }
    });

    $('button.header-catalog__heading').click(function() {
      var headerCatalog = $('nav.header-catalog');
      if (headerCatalog.is('.header-catalog--active')) {
        headerCatalog.removeClass('header-catalog--active');
      } else {
        headerCatalog.addClass('header-catalog--active');
      }
    });

    $('button.header-catalog__toggle').click(function() {
      var headerCatalogToggle = $(this);
      var headerCatalogSubmenu = headerCatalogToggle.next('ul.header-catalog__sub-menu');
      if (headerCatalogSubmenu.is('.header-catalog__sub-menu--active')) {
        headerCatalogToggle.removeClass('header-catalog__toggle--active');
        headerCatalogSubmenu.removeClass('header-catalog__sub-menu--active');
      } else {
        headerCatalogToggle.addClass('header-catalog__toggle--active');
        headerCatalogSubmenu.addClass('header-catalog__sub-menu--active');
      }
    });

    $('.header input.search__input').focus(function() {
      $('.header .search').addClass('search--active');
    });

    $('button.search-toggle').click(function() {
      $('.header .search').addClass('search--mobile-active');
      $('.header .search__input').focus();
    });

    $(document).click(function() {
      $('nav.header-catalog').removeClass('header-catalog--active');
      $('.header .search').removeClass('search--mobile-active');
      $('.header .search').removeClass('search--active');
    });

    $('nav.header-catalog, button.search-toggle, .header .search').click(function(evt) {
      evt.stopPropagation();
    });

    $('button.sidebar-nav__toggle').click(function() {
      var sidebarNavToggle = $(this);
      var sidebarNavSubMenu = sidebarNavToggle.next('ul.sidebar-nav__sub-menu');
      if (sidebarNavSubMenu.is('.sidebar-nav__sub-menu--active')) {
        sidebarNavSubMenu.removeClass('sidebar-nav__sub-menu--active');
      } else {
        sidebarNavSubMenu.addClass('sidebar-nav__sub-menu--active');
      }
    });

    $('button.filter-toggle').click(function() {
      var filter = $('.filter');
      var pickedFilter = $('.picked-filter');
      if (filter.is('.filter--active')) {
        filter.removeClass('filter--active');
        pickedFilter.removeClass('picked-filter--hidden');
      } else {
        filter.addClass('filter--active');
        pickedFilter.addClass('picked-filter--hidden');
      }
    });

    var mainSliderClass = '.main-slider';
    if ($(mainSliderClass).length) {
      var mainSlider = new Swiper(mainSliderClass, {
        pagination: {
          el: mainSliderClass + '__pagination',
          type: 'bullets',
          clickable: true,
        },
        speed: 1000,
        autoplay: {
          delay: 5000,
        },
        loop: true,
        simulateTouch: false,
        a11y: {
          prevSlideMessage: 'Предыдущий слайд',
          nextSlideMessage: 'Следующий слайд',
          paginationBulletMessage: 'Перейти к слайду {{index}}',
        },
      });
      $('.main-slider .swiper-slide').on('mouseover', function() {
        mainSlider.autoplay.stop();
      });

      $('.main-slider .swiper-slide').on('mouseout', function() {
        mainSlider.autoplay.start();
      });
    }

    var servicesSliderClass = '.services-slider';
    if ($(servicesSliderClass).length) {
      var servicesSlider = new Swiper(servicesSliderClass, {
        pagination: {
          el: servicesSliderClass + '__pagination',
          type: 'bullets',
          clickable: true,
        },
        speed: 1000,
        autoplay: {
          delay: 5000,
        },
        loop: true,
        simulateTouch: false,
        a11y: {
          prevSlideMessage: 'Предыдущий слайд',
          nextSlideMessage: 'Следующий слайд',
          paginationBulletMessage: 'Перейти к слайду {{index}}',
        },
      });
      $('.services-slider .swiper-slide').on('mouseover', function() {
        servicesSlider.autoplay.stop();
      });
      $('.services-slider .swiper-slide').on('mouseout', function() {
        servicesSlider.autoplay.start();
      });
    }

    var solutionsClass = '.solutions';
    if ($(solutionsClass).length) {
      var solutions = new Swiper('.solutions__items', {
        navigation: {
          prevEl: '.solutions__arrow--prev',
          nextEl: '.solutions__arrow--next',
        },
        pagination: {
          el: solutionsClass + '__pagination',
          type: 'bullets',
          clickable: true,
        },
        autoplay: {
          delay: 5000,
        },
        loop: true,
        slidesPerView: 1,
        slidesPerGroup: 1,
        simulateTouch: false,
        breakpoints: {
          500: {
            slidesPerView: 2,
          },
          1024: {
            speed: 500,
            slidesPerView: 4,
          },
        },
        a11y: {
          prevSlideMessage: 'Предыдущий слайд',
          nextSlideMessage: 'Следующий слайд',
          paginationBulletMessage: 'Перейти к слайду {{index}}',
        },
      });
      $('.solutions .swiper-slide').on('mouseover', function() {
        solutions.autoplay.stop();
      });
      $('.solutions .swiper-slide').on('mouseout', function() {
        solutions.autoplay.start();
      });
    }

    var photoGalleryBlock = 'photo-gallery';
    if ($('.' + photoGalleryBlock).length) {
      $('.' + photoGalleryBlock).each(function() {
        var photoSliderThumbs = new Swiper($(this).find('.' + photoGalleryBlock + '__thumbs'), {
          navigation: {
            prevEl: $(this).find('.photo-gallery__thumbs-wrap .photo-gallery__arrow--prev'),
            nextEl: $(this).find('.photo-gallery__thumbs-wrap .photo-gallery__arrow--next'),
          },
          spaceBetween: 10,
          slidesPerView: 2,
          loop: true,
          grabCursor: true,
          watchSlidesVisibility: true,
          watchSlidesProgress: true,
          breakpoints: {
            540: {
              slidesPerView: 3,
              spaceBetween: 15,
            },
            1024: {
              slidesPerView: 5,
              spaceBetween: 22,
            },
          },
        });
        var photoGallery = new Swiper($(this).find('.' + photoGalleryBlock + '__images'), {
          thumbs: {
            swiper: photoSliderThumbs,
          },
          navigation: {
            prevEl: $(this).find('.photo-gallery__images .photo-gallery__arrow--prev'),
            nextEl: $(this).find('.photo-gallery__images .photo-gallery__arrow--next'),
          },
          loop: true,
          speed: 500,
          simulateTouch: false,
          a11y: {
            prevSlideMessage: 'Предыдущий слайд',
            nextSlideMessage: 'Следующий слайд',
            paginationBulletMessage: 'Перейти к слайду {{index}}',
          },
        });
      });
    }

    var productGalleryBlock = 'product-gallery';
    if ($('.' + productGalleryBlock).length) {
      var productGalleryThumbsWrap = $('.product-gallery__thumbs-wrap');
      if (productGalleryThumbsWrap.length) {
        var productSliderThumbs = new Swiper('.' + productGalleryBlock + '__thumbs', {
          navigation: {
            prevEl: '.' + productGalleryBlock + '__arrow--prev',
            nextEl: '.' + productGalleryBlock + '__arrow--next',
          },
          spaceBetween: 5,
          slidesPerView: 4,
          loop: true,
          grabCursor: true,
          watchSlidesVisibility: true,
          watchSlidesProgress: true,
          breakpoints: {
            540: {
              spaceBetween: 15,
              direction: 'vertical',
            },
          },
        });
      }
      var slider = new Swiper('.' + productGalleryBlock + '__images', {
        navigation: {
          prevEl: '.product-gallery__images-arrow--prev',
          nextEl: '.product-gallery__images-arrow--next',
        },
        thumbs: {
          swiper: productSliderThumbs,
        },
        loop: true,
        speed: 500,
        breakpoints: {
          540: {
            direction: 'vertical',
          },
        },
        simulateTouch: false,
        a11y: {
          prevSlideMessage: 'Предыдущий слайд',
          nextSlideMessage: 'Следующий слайд',
          paginationBulletMessage: 'Перейти к слайду {{index}}',
        },
      });
    }

    $('input.add-file__input').change(function() {
      var el = $(this);
      var value = el.val();
      var name = el.closest('div.add-file').find('div.add-file__name');
      var placeholder = name.data('text');
      name.text(value.replace(/.+[\\\/]/, '')).addClass('changed');
      if (value === '') {
        name.text(placeholder).removeClass('changed');
      }
    });

    if ($('.album').length) {
      $('.album__link').magnificPopup({
        type: 'image',
        gallery: {
          enabled: true,
          navigateByImgClick: false,
          tCounter: '',
        },
        mainClass: 'mfp-fade',
      });
    }

    if ($('.product-gallery__images').length) {
      var productGalleryCount = $('.product-gallery__images .swiper-slide:not(.swiper-slide-duplicate)').length;
      var productGalleryImages = $(
        '.product-gallery .swiper-slide:not(.swiper-slide-duplicate) .product-gallery__link'
      );
      if (productGalleryCount == 2) {
        productGalleryImages = $('.product-gallery__link');
      }
      productGalleryImages.magnificPopup({
        type: 'image',
        gallery: {
          enabled: true,
          navigateByImgClick: false,
          tCounter: '',
        },
        mainClass: 'mfp-fade',
      });
    }

    $('input.range__slider').each(function() {
      var range = $(this);
      var inputFrom = range.closest('div.range').find('input.range__from');
      var inputTo = range.closest('div.range').find('input.range__to');
      range.ionRangeSlider({
        type: 'double',
        hide_min_max: true,
        hide_from_to: true,
        onStart: updateInputs,
        onChange: updateInputs,
        onFinish: finishInputs,
      });

      function updateInputs(data) {
        inputFrom.val(data.from);
        inputTo.val(data.to);
      }

      function finishInputs(data) {
        window.dispatchEvent(
          new CustomEvent('update-slug', {
            detail: { slug: inputTo.closest('filedset').data('slug') },
          })
        );
      }

      var slider = range.data('ionRangeSlider');
      inputFrom.keyup(function() {
        slider.update({
          from: inputFrom.val(),
        });
      });
      inputTo.keyup(function() {
        slider.update({
          to: inputTo.val(),
        });
      });
    });

    $('.layout__button').click(function() {
      var layoutButton = $(this);
      $('.layout__button').removeClass('layout__button--active');
      layoutButton.addClass('layout__button--active');
      if (layoutButton.is('.layout__button--list')) {
        $('.items-accessory').addClass('items-accessory--list');
        $('.items-news').addClass('items-news--list');
      } else {
        $('.items-accessory').removeClass('items-accessory--list');
        $('.items-news').removeClass('items-news--list');
      }
    });
  });
})(jQuery);

// a11y tabs
(function() {
  var tabs = document.querySelectorAll('.tabs');
  Array.prototype.forEach.call(tabs, function(tabbed) {
    var tablist = tabbed.querySelector('ul');
    var tabs = tablist.querySelectorAll('a');
    var panels = tabbed.querySelectorAll('section');
    var switchTab = function switchTab(oldTab, newTab) {
      newTab.focus();
      newTab.removeAttribute('tabindex');
      newTab.setAttribute('aria-selected', 'true');
      oldTab.removeAttribute('aria-selected');
      oldTab.setAttribute('tabindex', '-1');
      var index = Array.prototype.indexOf.call(tabs, newTab);
      var oldIndex = Array.prototype.indexOf.call(tabs, oldTab);
      panels[oldIndex].hidden = true;
      panels[index].hidden = false;
    };
    tablist.setAttribute('role', 'tablist');
    Array.prototype.forEach.call(tabs, function(tab, i) {
      tab.setAttribute('role', 'tab');
      tab.setAttribute('id', 'tab' + (i + 1));
      tab.setAttribute('tabindex', '-1');
      tab.parentNode.setAttribute('role', 'presentation');
      tab.addEventListener('click', function(e) {
        e.preventDefault();
        var currentTab = tablist.querySelector('[aria-selected]');
        if (e.currentTarget !== currentTab) {
          switchTab(currentTab, e.currentTarget);
        }
      });
      tab.addEventListener('keydown', function(e) {
        var index = Array.prototype.indexOf.call(tabs, e.currentTarget);
        var dir = e.which === 37 ? index - 1 : e.which === 39 ? index + 1 : e.which === 40 ? 'down' : null;
        if (dir !== null) {
          e.preventDefault();
          dir === 'down' ? panels[i].focus() : tabs[dir] ? switchTab(e.currentTarget, tabs[dir]) : void 0;
        }
      });
    });
    Array.prototype.forEach.call(panels, function(panel, i) {
      panel.setAttribute('role', 'tabpanel');
      panel.setAttribute('tabindex', '-1');
      panel.setAttribute('aria-labelledby', tabs[i].id);
      panel.hidden = true;
    });

    var legacy = true;
    tabs.forEach((tab, index)=>{
      if('true'===tab.getAttribute('aria-selected')){
        tabs[index].removeAttribute('tabindex');
        tabs[index].setAttribute('aria-selected', 'true');
        panels[index].hidden = false;
        legacy = false;
      }
    });

    if(legacy){
      tabs[0].removeAttribute('tabindex');
      tabs[0].setAttribute('aria-selected', 'true');
      panels[0].hidden = false;
    }

  });
})();
// end a11y tabs
