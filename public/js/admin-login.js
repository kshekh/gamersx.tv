$(document).ready(function () {
  if ((document.URL).includes("admin/login") || (document.URL).includes("admin/resetting/request")) {
    $(".login-page").css('background', "#373A41")
    let domain = (new URL(document.URL));

    $(".login-logo img").attr('src', "/images/logo.png").css({
      width: "100%",
    })
    $(".login-box-body").css("background", "#121212").css({
        border: '2px solid #6441A5',
      }
    )
    $(".login-box-body form").attr("autocomplete", "off")
    $(".form-control").css({
        background: "#373A41",
        color: "white"
      }
    )
    $(".login-logo > a > span").text("")
    if ((document.URL).includes("admin/resetting/request"))
      $(".login-box-msg").text("Password Reset").css({
        color: "white"
      })
    if ((document.URL).includes("admin/login"))
      $(".login-box-msg").text("Login").css({
        color: "white"
      })
  }
})