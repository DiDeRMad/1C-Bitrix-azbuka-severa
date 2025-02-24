/**
 * Таймер для повторного введения смс кода
 */
function initTimer() {
  var sms = 59
  var timerId = setInterval(function () {
    $("#code-timer").html(sms)
    sms--
    if (sms === 0) {
      clearInterval(timerId)
      $(".modal-auth__info-text").fadeOut()
      $("#send-code").removeClass("hidden-sms")
    }
  }, 1000)
}

/**
 * Инициализация функции маски для поля телефона
 * Документация: https://imask.js.org/guide.html
 */
function initPhoneMask() {
  $("input[type=tel]").each(function (index, element) {
    var mask = IMask(element, {
      mask: [
        {
          mask: "+7 (000) 000-00-00",
          startsWith: "+7",
          country: "Russia",
        },
        {
          mask: "+7 (000) 000-00-00",
          startsWith: "7",
          country: "Russia",
        },
        {
          mask: "0 (000) 000-00-00",
          startsWith: "8",
          country: "Russia",
        },
        {
          mask: "+7 (000) 000-00-00",
          startsWith: "",
          country: "unknown",
        },
      ],
      dispatch: function dispatch(appended, dynamicMasked) {
        var number = (dynamicMasked.value + appended).replace(/\D/g, "")
        return dynamicMasked.compiledMasks.find(function (m) {
          return number.indexOf(m.startsWith) === 0
        })
      },
    })
    $(this).blur(function () {
      var maskValue = mask.unmaskedValue
      var startWith = 10

      if (maskValue.charAt(0) === "8") {
        startWith = 11
      }

      if (maskValue.length < startWith) {
        mask.value = ""
      }
    })
  })
}

function toggleActive() {
  $(".js-extra-category").on("click", function () {
    $(this).toggleClass("active")
    $(".extra-category__list--add-wrapper").slideToggle()
  })
}

function productCounterReload() {
  var container = $(this).siblings(".product-counter__all-in")
  var units = container.find(".js-product-units").val()
  var step = container.find(".js-product-step").val()
  var counter = container.find(".js-really-quantity").val()
  var result = container.find(".product-counter__input")
  var parentTotal = container.parents(".product-item__control")
  var cartTotal = parentTotal.find(".js-product-cart-total")
  var price = parentTotal.find(".js-product-price").val()
  var addTotal = ((counter * (step * 100)) / 100)
    .toString()
    .match(/^[^\.]+\.{0,1}.{0,2}/gi)
  var separator = " "
  var total = "" + addTotal + " " + units

  var totalPrice = counter * price

  var formatPrice =
    "" +
    totalPrice
      .toString()
      .replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g, "$1" + separator) +
    " \u20BD"

  cartTotal.html(formatPrice)
  result.val(total)
}

function productCounter() {
  $(document).on("click", ".js-product-minus", function (e) {
    e.preventDefault()
    var counter = $(this)
      .siblings(".product-counter__all-in")
      .find(".js-really-quantity")
    var parent = $(this).parents(".product-counter")
    var reload = productCounterReload.bind(this)

    if (counter.val() > 1) {
      var inputVal = parseInt(counter.val()) - 1
      counter.val(inputVal)
      reload()
    } else {
      parent.removeClass("active")
      $(this).parents(".product-item").removeClass("active")
      parent.siblings(".product-item__btn-cart").addClass("active")
      $(this).parents(".product-card__control").removeClass("active")
      counter.val(1)
      reload()
    }
  })
  $(document).on("click", ".js-product-plus", function (e) {
    e.preventDefault()
    var counter = $(this)
      .siblings(".product-counter__all-in")
      .find(".js-really-quantity")
    var reload = productCounterReload.bind(this)
    var inputVal = parseInt(counter.val()) + 1
    counter.val(inputVal)
    reload()
  })
}

function clipCommunity() {
  var height = $(".community__list").height()
  var item = $(".community__item").height()
  var total = height - (item / 3) * 2
  $(".community__list-wrapper").height(total)
}

function toggleCategory(selector) {
  $(document).on("click", selector, function () {
    $(selector).toggleClass("active")
    $(".header-category").toggleClass("active")

    if (window.matchMedia("(min-width: 1024px)").matches) {
      refreshCategory()
    }

    toggleOverlay()
  })
}

function desktopCategory() {
  $(".header-category-menu__item").hover(function () {
    var id = $(this).data("category")
    $(this).each(function () {
      $(".header-category-menu__item").removeClass("active")
      $(this).addClass("active")
    })
    $(".subcategory-menu").each(function () {
      var categoryId = $(this).data("category-id")

      if (categoryId == id) {
        $(this).addClass("active")
      } else if (id) {
        $(this).removeClass("active")
      }
    })
  })
  $(".subcategory-menu").hover(function () {
    var id = $(this).data("category-id")
    $(this).addClass("active")
    $(".header-category-menu__item").each(function () {
      var categoryId = $(this).data("category")

      if (categoryId == id && id !== undefined) {
        $(this).addClass("active")
      } else if (id) {
        $(this).removeClass("active")
      }
    })
  })
  $(".subcategory-menu__item").hover(function () {
    var id = $(this).data("sub-category")
    $(this).each(function () {
      $(".subcategory-menu__item").removeClass("active")
      $(this).addClass("active")
    })
    $(".subcategory-menu__submenu").each(function () {
      var categoryId = $(this).data("sub-category-id")

      if (categoryId && categoryId == id) {
        $(this).addClass("active")
      } else if (id) {
        $(this).removeClass("active")
      }
    })
  })
  $(".subcategory-menu__submenu").hover(function () {
    var id = $(this).data("sub-category-id")
    $(".subcategory-menu__item").each(function () {
      var categoryId = $(this).data("sub-category")

      if (categoryId && categoryId == id) {
        $(this).addClass("active")
      } else if (id) {
        $(this).removeClass("active")
      }
    })
  })
}

function mobileCategory() {
  var menu = $(".header-category-menu")
  $(document).on("click", ".category-back-btn", function (e) {
    e.preventDefault()
    var parent = $(this).parents(".subcategory-menu")
    parent.removeClass("active")
    menu.removeClass("disabled")
  })
  $(document).on("click", ".subcategory-to-btn", function (e) {
    e.preventDefault()
    var parent = $(this).parents(".subcategory-menu")
    var smallParent = $(this).parents(".subcategory-menu__menu")
    var id = $(this).parents(".subcategory-menu__item").data("sub-category")
    parent.find(".subcategory-menu__submenu").each(function () {
      var menuId = $(this).data("sub-category-id")

      if (id === menuId) {
        smallParent.hide()
        $(this).addClass("active")
      }
    })
  })
  $(document).on("click", ".subcategory-back-btn", function (e) {
    e.preventDefault()
    var parent = $(this).parents(".subcategory-menu__submenu")
    var subMenu = parent.siblings(".subcategory-menu__menu")
    parent.removeClass("active")
    subMenu.show()
  })
  $(document).on("click", ".header-category-menu__item-arrow", function () {
    var parentId = $(this)
      .parents(".header-category-menu__item")
      .data("category")

    if (parentId) {
      $(".subcategory-menu").each(function () {
        var categoryId = $(this).data("category-id")

        if (categoryId === parentId) {
          $(this).addClass("active")
          menu.addClass("disabled")
        } else {
          $(this).removeClass("active")
        }
      })
    }
  })
}

function goToHead() {
  var btn = $(".go-to-head")
  var fixedHeader = $(".header")
  $(window).on("scroll", function () {
    var scroll = $(window).scrollTop()
    var screenHeight = $(window).height() + 500

    if (window.matchMedia("(min-width: 1281px)").matches) {
      if (scroll > 55) {
        fixedHeader.addClass("fixed")
      } else {
        fixedHeader.removeClass("fixed")
      }
    }

    if (
      window.matchMedia("(max-width: 1280px)").matches &&
      window.matchMedia("(min-width: 1024px)").matches
    ) {
      if (scroll > 49) {
        fixedHeader.addClass("fixed")
      } else {
        fixedHeader.removeClass("fixed")
      }
    }

    if (scroll > screenHeight) {
      btn.show()
    } else {
      btn.hide()
    }
  })
  btn.on("click", function () {
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    )
  })
}

function toggleOverlay() {
  var overlay = $(".header-overlay")
  var body = $("body")
  overlay.toggleClass("active")
  body.toggleClass("hidden")
}

function productSliderInit() {
  if ($("div").is(".product-slider-general")) {
    $(".product-slider-general").slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      rows: 0,
      arrows: false,
      fade: true,
      asNavFor: ".product-slider-small",
      responsive: [
        {
          breakpoint: 1024,
          settings: {
            dots: true,
            arrows: true,
          },
        },
      ],
    })
  }

  if ($("div").is(".product-slider-small")) {
    $(".product-slider-small").slick({
      slidesToShow: 3,
      slidesToScroll: 1,
      rows: 0,
      asNavFor: ".product-slider-general",
      vertical: true,
      verticalSwiping: true,
      arrows: true,
      dots: false,
      focusOnSelect: true,
      responsive: [
        {
          breakpoint: 1281,
          settings: {
            vertical: false,
            verticalSwiping: false,
          },
        },
      ],
    })
  }
}

function refreshCategory() {
  var item = $(".header-category-menu__item:first")
  var itemId = item.data("category")
  $(".subcategory-menu__item").each(function () {
    $(this).removeClass("active")
  })
  $(".subcategory-menu__submenu").each(function () {
    $(this).removeClass("active")
  })
  $(".header-category-menu__item").each(function () {
    $(this).removeClass("active")
  })
  $(".subcategory-menu").each(function () {
    if ($(this).data("category-id") === itemId) {
      $(this).addClass("active")
      var first = $(this).find(".subcategory-menu__item:first")
      var firstId = first.data("sub-category")
      first.addClass("active")
      $('[data-sub-category-id="'.concat(firstId, '"]')).addClass("active")
    } else {
      $(this).removeClass("active")
    }
  })
  item.addClass("active")
}

function reviewsSliderInit() {
  $(".reviews-slider").slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    rows: 0,
    arrows: true,
    dots: true,
    infinite: true,
    responsive: [
      {
        breakpoint: 1660,
        settings: {
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ],
  })
}

function initTabs() {
  $(".contact-tabs__control-item").on("click", function (e) {
    e.preventDefault()
    var id = $(this).attr("id")
    // console.log(id);
    $(".contact-tabs__control-item,.contact-tabs__content-item").removeClass(
      "active"
    )
    $(this).addClass("active")
    $('.contact-tabs__content-item[data-id="' + id + '"]').addClass("active")
  })
}
/**
 * Инициализация range слайдера для цен
 * Документация: https://refreshless.com/nouislider/
 */

function initRangeSlider() {
  var sliderEl = document.getElementsByClassName("price-range-slider"),
    rangeValueStart = $(".range-input__start"),
    rangeValueEnd = $(".range-input__end"),
    min = Number.parseInt(rangeValueStart.data("min")),
    max = Number.parseInt(rangeValueEnd.data("max")),
    start = Number.parseInt(rangeValueStart.val()),
    end = Number.parseInt(rangeValueEnd.val())

  if ($(".price-range-slider").length > 0) {
    noUiSlider.create(sliderEl[0], {
      start: [start, end],
      connect: true,
      range: {
        min: min,
        max: max,
      },
    })
    sliderEl[0].noUiSlider.on("update", function (values, handle) {
      rangeValueStart.val(Math.round(values[0]))
      rangeValueEnd.val(Math.round(values[1]))
    })
    rangeValueStart.on("change", function () {
      sliderEl[0].noUiSlider.set([this.value, null])
    })
    rangeValueEnd.on("change", function () {
      sliderEl[0].noUiSlider.set([null, this.value])
    })
  }
}

function accordionInit() {
  $(".js-filter-toggle").on("click", function (e) {
    e.preventDefault()
    $(this).toggleClass("active")
    $(this).siblings(".filter__content").slideToggle()
  })
}

function changePositionSidebar() {
  var sidebar = $(".catalog__sidebar")
  sidebar.insertAfter($(".sort-block"))
}

function toggleMobileFilter() {
  $(".filter__content").each(function () {
    $(this).css({
      display: "flex",
    })
  })
  $(document).on("click", ".mobile-btn-filter", function () {
    var parent = $(this).parents(".sort-block")
    $(this).toggleClass("active")
    parent.toggleClass("active")
    $(".catalog__sidebar").toggleClass("active")
  })
}
/**
 * Инициализация кастомного скролла
 * Документация: https://github.com/idiotWu/smooth-scrollbar
 */

function initScrollBar(elements) {
  if ($(elements).length > 0) {
    $(elements).each(function () {
      var scrollbar = window.Scrollbar
      scrollbar.init($(this)[0], {
        alwaysShowTracks: true,
      })
    })
  }
}

function mobileToggleCart() {
  $(".cart-sidebar__price-wrapper").on("click", function () {
    $(this).toggleClass("active")
    $(".cart-sidebar__mobile-toggle").slideToggle()
  })
}

function initDeliveryMethods() {
  $(".js-delivery-methods").on("click", function () {
    $(".checkout-block-item__delivery-wrapper").each(function () {
      $(this).removeClass("active")
    })
    $(this).parents(".checkout-block-item__delivery-wrapper").addClass("active")
  })
}
/**
 * Инициализация кастомного селекта
 * Источник: https://select2.org/
 */

function initCustomSelect() {
  $(".select-custom").select2({
    minimumResultsForSearch: Infinity,
  })
}

function initModal() {
  $('[data-modal="true"]').on("click", function (e) {
    e.preventDefault()
    var modalId = $(this).data("modal-id")
    $(modalId).modal({
      fadeDuration: 400,
    })
  })
}

function clearCart() {
  $(".js-clear-cart").on("click", function () {
    $(".clear-modal").addClass("active")

    if (window.matchMedia("(max-width: 767px)").matches) {
      $(".clear-modal-overlay").addClass("active")
      $("body").addClass("hidden")
    }
  })
  $(".clear-modal .btn").on("click", function (e) {
    $(".clear-modal").removeClass("active")
  })
  $(".clear-modal-overlay").on("click", function () {
    $(".clear-modal").removeClass("active")
    $(".clear-modal-overlay").removeClass("active")
    $("body").removeClass("hidden")
  })
}

$(function () {
  objectFitImages()
  //initPhoneMask()
  toggleActive()
  productCounter()
  clipCommunity()
  goToHead()
  productSliderInit()
  initRangeSlider()
  initCustomSelect()
  initDeliveryMethods()
  initTabs()
  initModal()
  clearCart()

  $(".js-short-recipe").on("click", function () {
    var dataLong = $(this).data("long")
    $(this).addClass("active")
    $(this).html(dataLong)
  })

  $(".product-card__buy").on("click", function () {
    $(this).parents(".product-card__control").addClass("active")
  })

  if (window.matchMedia("(min-width: 1024px)").matches) {
    desktopCategory()
    accordionInit()
    initScrollBar(".cart__content")
    $(".product-slider").slick({
      slidesToShow: 5,
      slidesToScroll: 1,
      arrows: true,
      dots: true,
      infinite: true,
      responsive: [
        {
          breakpoint: 1501,
          settings: {
            slidesToShow: 4,
          },
        },
      ],
    })
    toggleCategory(".js-category-btn")
  }

  if (window.matchMedia("(min-width: 320px)").matches) {
    reviewsSliderInit()
  }

  if (window.matchMedia("(max-width: 1023px)").matches) {
    mobileCategory()
    toggleCategory(".js-category-mobile-btn")
    changePositionSidebar()
    toggleMobileFilter()
    $(".header-mobile-right__menu").on("click", function () {
      $(".header-top__wrapper").toggleClass("active")
      $("body").toggleClass("hidden")
      $(".header-overlay").toggleClass("r-active")

      if (window.matchMedia("(max-width: 767px)").matches) {
        $(".header-logo").addClass("active")
      }
    })
    $(".header-top__close-btn").on("click", function () {
      $(".header-top__wrapper").removeClass("active")
      $("body").removeClass("hidden")
      $(".header-overlay").removeClass("r-active")
    })

    if (window.matchMedia("(max-width: 767px)").matches) {
      mobileToggleCart()
    }
  }

  $(document).on("click", ".header-overlay", function () {
    $(".header-category").removeClass("active")
    $(".header-bottom__btn").removeClass("active")
    $(".header-top__wrapper").removeClass("active")
    $("body").removeClass("hidden")
    $(this).removeClass("active")
    $(this).removeClass("r-active")
  })

  // $(document).on("click", function (e) {
  //   var search = $(".header-search")

  //   if (!search.is(e.target) && search.has(e.target).length === 0) {
  //     search.removeClass("active")

  //     if (window.matchMedia("(max-width: 767px)").matches) {
  //       search.find(".header-search__field").val("")
  //     }
  //   }
  // })

  $(document).on("click", ".header-search", function () {
    $(this).addClass("active");

    // Automatically focus on the input field to show the keyboard
    $(this).find(".header-search__field").focus();
  });

  $(document).on("click", ".product-item__btn-cart", function () {
    var context = $(this).siblings(".product-counter").find(".js-product-minus")
    var reload = productCounterReload.bind(context)
    $(this).siblings(".product-counter").addClass("active")
    $(this).parents(".product-item").addClass("active")
    $(this).removeClass("active")
    reload()
  })

  $(document).on("click", ".product-card__buy", function () {
    var context = $(this).siblings(".product-counter").find(".js-product-minus")
    var reload = productCounterReload.bind(context)
    $(this).siblings(".product-counter").addClass("active")
    $(this).parents(".product-item").addClass("active")
    $(this).removeClass("active")
    reload()
  })

  $(document).on("click", ".header-search", function () {
    $(this).addClass("active")
  })

  $(".header-search__clear").on("click", function (e) {
    var parent = $(this).parents(".header-search")
    $(this).siblings(".header-search__field").val("")

    if (window.matchMedia("(max-width: 767px)").matches) {
      e.stopPropagation()
      parent.removeClass("active")
    }
  })

  $(".big-bs").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    dots: true,
    infinite: true,
    autoplay: true,
    autoplaySpeed: 13000,
  })

  $(".article-slider").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    dots: true,
    infinite: true,
    rows: 0,
  })

  /*Цели*/
  $(".header-bottom__cart").on("click", function () {
    ym(53235643, "reachGoal", "vkorziny")
    //console.log('goal - vkorziny');
  })

  $(".checkout-total__send").on("click", function () {
    ym(53235643, "reachGoal", "orderSuccess")
    //console.log('goal - orrderSuccess');
  })

  $("#one_click_send").on("click", function (e) {
    e.preventDefault()
    var name = $("#modal-one-click").find('input[name="CLIENT_NAME"]').val()
    var phone = $("#modal-one-click").find('input[name="CLIENT_PHONE"]').val()
    var item = $("#modal-one-click").find('input[name="ITEM"]').val()
    var comment = $("#modal-one-click").find('textarea[name="COMMENT"]').val()
    var action = "one_click_send"
    grecaptcha.ready(function () {
      grecaptcha
        .execute("6Lf6sqcpAAAAAJQDz1LxT3_zLyHFxSY62cpPhBK0", { action: action })
        .then(function (token) {
          $.ajax({
            url: "/local/ajax/check_captcha.php",
            method: "post",
            dataType: "html",
            data: { token: token, action: action },
            success: function (data) {
              data = JSON.parse(data)
              //console.log(data);
              if (data === true) {
                $.ajax({
                  url: "/local/ajax/one-click-buy.php",
                  method: "post",
                  dataType: "html",
                  data: {
                    CLIENT_NAME: name,
                    CLIENT_PHONE: phone,
                    COMMENT: comment,
                    ITEM: item,
                  },
                  success: function (data) {
                    console.log(data)
                    if (data === "good") {
                      $("#one_click_form").hide()
                      $("#one_click_success").show()
                      $("#modal-one-click")
                        .find(".modal-left__description")
                        .hide()
                    }
                  },
                })
              }
            },
          })
        })
    })
  })

  $("#koptil-btn").on("click", function () {
    var action = "koptil"
    grecaptcha.ready(function () {
      grecaptcha
        .execute("6Lf6sqcpAAAAAJQDz1LxT3_zLyHFxSY62cpPhBK0", { action: action })
        .then(function (token) {
          $.ajax({
            url: "/local/ajax/check_captcha.php",
            method: "post",
            dataType: "html",
            data: { token: token, action: action },
            success: function (data) {
              data = JSON.parse(data)
              //console.log(data);
              if (data === true) {
                $.ajax({
                  url: "/local/ajax/form-koptil.php",
                  method: "post",
                  dataType: "html",
                  data: { phone: $("#koptil-phone").val() },
                  success: function (data) {
                    $("#koptil-phone").hide()
                    $("#koptil-btn").hide()
                    $("#good-kotil").show()
                  },
                })
              }
            },
          })
        })
    })
  })

  $("#cat-koptil-btn").on("click", function () {
    var action = "koptil-cat"
    grecaptcha.ready(function () {
      grecaptcha
        .execute("<?= RECAPTHA_SITEKEY ?>", { action: "action" })
        .then(function (token) {
          $.ajax({
            url: "/local/ajax/check_captcha.php",
            method: "post",
            dataType: "html",
            data: { token: token, action: action },
            success: function (data) {
              data = JSON.parse(data)
              console.log(data)
              if (data === true) {
                $.ajax({
                  url: "/local/ajax/form-koptil.php",
                  method: "post",
                  dataType: "html",
                  data: { phone: $("#cat-koptil-phone").val() },
                  success: function (data) {
                    console.log(data)
                    $("#cat-koptil-phone").hide()
                    $("#cat-koptil-btn").hide()
                    $("#cat-good-kotil").show()
                  },
                })
              }
            },
          })
        })
    })
  })

  $("#callback_form_send").on("click", function (e) {
    e.preventDefault()
    var name = $("#callback_form").find('input[name="CLIENT_NAME"]').val()
    var phone = $("#callback_form").find('input[name="CLIENT_PHONE"]').val()
    var comment = $("#callback_form").find('textarea[name="COMMENT"]').val()

    var captcha_word = $("#callback_form")
      .find('input[name="captcha_word"]')
      .val()
    var captcha_code = $("#callback_form")
      .find('input[name="captcha_code"]')
      .val()
console.log("reCAPTCHA Site Key:", "<?= RECAPTCHA_SITEKEY?>");

    var action = "callback_form_send"
    grecaptcha.ready(function () {
      grecaptcha
        .execute("6Lf6sqcpAAAAAJQDz1LxT3_zLyHFxSY62cpPhBK0", { action: action })
        .then(function (token) {
          $.ajax({
            url: "/local/ajax/check_captcha.php",
            method: "post",
            dataType: "html",
            data: { token: token, action: action },
            success: function (data) {
              data = JSON.parse(data)
              //console.log(data);
              //if (captcha_word == captcha_code) {
              if (data === true) {
                $.ajax({
                  url: "/local/ajax/form-callback-send.php",
                  method: "post",
                  dataType: "html",
                  data: {
                    CLIENT_NAME: name,
                    CLIENT_PHONE: phone,
                    COMMENT: comment,
                  },
                  success: function (data) {
                    console.log(data)
                    if (data === "good") {
                      $("#callback_form").hide()
                      $("#good-callback").show()
                      $("#modal-callback")
                        .find(".modal-left__description")
                        .hide()
                    }
                  },
                })
              }

              //}
            },
          })
        })
    })
  })

  $("#item_form_send").on("click", function (e) {
    e.preventDefault()
    var name = $("#modal-item-buy").find('input[name="CLIENT_NAME"]').val()
    var phone = $("#modal-item-buy").find('input[name="CLIENT_PHONE"]').val()
    var item = $("#modal-item-buy").find('input[name="ITEM"]').val()
    var comment = $("#modal-item-buy").find('textarea[name="COMMENT"]').val()
    var action = "item_form_send"
    grecaptcha.ready(function () {
      grecaptcha
        .execute("6Lf6sqcpAAAAAJQDz1LxT3_zLyHFxSY62cpPhBK0", { action: action })
        .then(function (token) {
          $.ajax({
            url: "/local/ajax/check_captcha.php",
            method: "post",
            dataType: "html",
            data: { token: token, action: action },
            success: function (data) {
              data = JSON.parse(data)
              //console.log(data);
              if (data === true) {
                $.ajax({
                  url: "/local/ajax/form-item-buy.php",
                  method: "post",
                  dataType: "html",
                  data: {
                    CLIENT_NAME: name,
                    CLIENT_PHONE: phone,
                    COMMENT: comment,
                    ITEM: item,
                  },
                  success: function (data) {
                    console.log(data)
                    if (data === "good") {
                      $("#item-buy_form").hide()
                      $("#good-item").show()
                      $("#modal-item-buy")
                        .find(".modal-left__description")
                        .hide()
                    }
                  },
                })
              }
            },
          })
        })
    })
  })

  $("#prepare_filter").on("click", function () {
    $(".range-input__start").change()
    $(".range-input__end").change()

    setTimeout(() => $("#set_filter").click(), 1500)
  })

  $("#message_form_send").on("click", function (e) {
    e.preventDefault()
    var name = $("#message_form").find('input[name="CLIENT_NAME"]').val()
    var phone = $("#message_form").find('input[name="CLIENT_PHONE"]').val()
    var comment = $("#message_form").find('textarea[name="COMMENT"]').val()
    var action = "message_form_send"
    grecaptcha.ready(function () {
      grecaptcha
        .execute("6Lf6sqcpAAAAAJQDz1LxT3_zLyHFxSY62cpPhBK0", { action: action })
        .then(function (token) {
          $.ajax({
            url: "/local/ajax/check_captcha.php",
            method: "post",
            dataType: "html",
            data: { token: token, action: action },
            success: function (data) {
              data = JSON.parse(data)
              //console.log(data);
              if (data === true) {
                $.ajax({
                  url: "/local/ajax/form-message-send.php",
                  method: "post",
                  dataType: "html",
                  data: {
                    CLIENT_NAME: name,
                    CLIENT_PHONE: phone,
                    COMMENT: comment,
                  },
                  success: function (data) {
                    if (data === "good") {
                      $("#message_form").hide()
                      $("#good-message").show()
                      $("#send-message").find(".modal-left__description").hide()
                    }
                  },
                })
              }
            },
          })
        })
    })
  })
})

$(document).ready(function () {
  function headerInfoPopup() {
    let popup = $(".header-info")
    let close = $(".header-info__close")

    close.on("click", function () {
      popup.addClass("hide")
    })
  }
  headerInfoPopup()

  $(document).on("click", "button.add-reciept", function (e) {
    e.preventDefault()
    let recId = $(this).attr("data-reciept")
    $.ajax({
      url: "/local/ajax/addrecieptfav.php",
      method: "post",
      dataType: "html",
      data: {
        recId: recId,
      },
      success: function (data) {
        //console.log(data);
        if (data == '"add"') {
          $("button.add-reciept span").text("Добавлено в избранное")
        } else if (data == '"delete"') {
          $("button.add-reciept span").text("Добавить в избранное")
        }
      },
    })
  })

  $("#review-add").on("submit", function (e) {
    e.preventDefault()
    let data = $(this).serialize()
    $.ajax({
      url: "/local/ajax/reviewadd.php",
      method: "post",
      data: data,
      success: function (res) {
        location.reload()
        /*  if (res) {
             if ('#reviews-ajax') {
                let answ = JSON.parse(res);
                 let htmlAdd = '<div class="reviews-slider__item">\n' +
                     '                <p class="reviews-slider__name">' + answ['NAME'] + '</p>\n' +
                     '                <p class="reviews-slider__description">' + answ['TEXT'] + '</p>\n' +
                     '                <div class="reviews-slider__meta">\n' +
                     '                    <p class="reviews-slider__date">' + answ['DATE_CREATED'] + '</p>\n' +
                     '                </div>\n' +
                     '                <p data-state="close" class="reviews-slider__name show_all_review">Читать далее</p>\n' +
                     '            </div>';
                 $('#reviews-ajax .slick-track').append(htmlAdd);
            } else {
                location.reload();
            }
        }*/
      },
    })
  })

  $(document).on("click", ".js-review-add", function () {
    let elemid = $(this).attr("data-elem")
    console.log(elemid)
    $("#revelemid").val(elemid)
  })

  /**
   * смарт-фильтр в каталоге - летающая кнопка "показать" если не показываем скролл - то летающая кнопка
   * это была первая реализация, потом переигали на Сбросить фильтр
   *
  if($(".ext-scroll").length == 0){
    var ext_show_btn = $(".filter__content .ext_show_button");
    var prepare_btn =  $("#prepare_filter");
    var diff = 15;
    var intCntChecked = 0;
    var pos = [];

    $(".filter__content .filter__content-checkbox").on("click", function(){
      pos = $(this).position();
      intCntChecked = $( ".filter__content input:checked" ).length;
      if(intCntChecked > 0){
        ext_show_btn.css("display","flex");
        prepare_btn.css("display","none");
      }
      else{
        ext_show_btn.css("display","none");
        prepare_btn.css("display","flex");
      }
      ext_show_btn.css("top",pos.top - diff);
    });

    $(".filter__name").on("click",function(){
      if ($(this).hasClass("active")) {
        if(prepare_btn.css("display") == "none") {
          prepare_btn.css("display","flex");
        }
      } else {
        if(ext_show_btn.css("display") == "flex") {
          prepare_btn.css("display","none");
        }
      }
    });

    $(".filter__content .ext_show_button").on("click",function(){
      $(".range-input__start").change();
      $(".range-input__end").change();

      setTimeout(() => $("#set_filter").click(), 500);
    });
  }*/

  /**
   * кнопка сбросить в фильтре у чекбоксов
   */

  $(".filter__content .filter__content-checkbox").on("click", function () {
    var intCntChecked = $(
      ".filter__content input[type=checkbox]:checked"
    ).length
    if (intCntChecked > 0) {
      $(this)
        .closest(".filter__content")
        .siblings(".filter__refresh")
        .addClass("show")
    } else {
      $(this)
        .closest(".filter__content")
        .siblings(".filter__refresh")
        .removeClass("show")
    }
  })

  $(".filter__refresh").on("click", function () {
    $(this)
      .siblings(".filter__content")
      .find("input[type=checkbox]:checked")
      .prop("checked", false)

    $(this).removeClass("show")

    $(".range-input__start").change()
    $(".range-input__end").change()
    setTimeout(() => $("#del_filter").click(), 1000)
  })

  $(
    ".filter__content .filter__content-checkbox input[type=checkbox]:checked"
  ).each(function () {
    $(this)
      .closest(".filter__content")
      .siblings(".filter__refresh")
      .addClass("show")
  })
})

document.addEventListener("DOMContentLoaded", () => {
  if (document.querySelector(".js-phone-validation")) {
    const phoneInput = document.querySelector(".js-phone-validation")

    phoneInput.addEventListener("input", (event) => {
      if (event.target.value.length < 16 && event.target.value != "+7") {
        phoneInput
          .closest(".checkout-block__form-item")
          .querySelector(".err-msg")
          .classList.add("err-msg--active")
      } else {
        phoneInput
          .closest(".checkout-block__form-item")
          .querySelector(".err-msg")
          .classList.remove("err-msg--active")
      }
    })

    const emailInput = document.querySelector(".js-email-validation")

    emailInput.addEventListener("input", (event) => {
      if (
        !event.target.value.match(
          /[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+/
        ) &&
        event.target.value != ""
      ) {
        emailInput
          .closest(".checkout-block__form-item")
          .querySelector(".err-msg")
          .classList.add("err-msg--active")
      } else {
        emailInput
          .closest(".checkout-block__form-item")
          .querySelector(".err-msg")
          .classList.remove("err-msg--active")
      }
    })
  }

	var lazyBackgrounds = [].slice.call(document.querySelectorAll(".lazy-background"));

	if ("IntersectionObserver" in window) {
		let lazyBackgroundObserver = new IntersectionObserver(function(entries, observer) {
			entries.forEach(function(entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add("visible");
					lazyBackgroundObserver.unobserve(entry.target);
				}
			});
		});

		lazyBackgrounds.forEach(function(lazyBackground) {
			lazyBackgroundObserver.observe(lazyBackground);
		});
	}

	document.querySelector('.product-card__img').style.opacity = '1';
})

// маска
document.addEventListener("DOMContentLoaded", () => {
  const phoneMaskInputs = document.querySelectorAll(".js-phone-mask", () => {
    phoneMaskInputs.forEach((phoneMaskInput) => {
      IMask(phoneMaskInput, {
        mask: "+{7}(000)000-00-00",
      })
    })
  })
})
