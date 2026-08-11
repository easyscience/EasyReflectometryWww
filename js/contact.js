(function() {

  "use strict"

  const site_key = "6LfjgU8bAAAAADn_z6MTsvSboq6oK8_5RD6CV2XI"

  grecaptcha.ready(function() {

    grecaptcha.execute(site_key, {action: "homepage"}).then(function(token) {
      //console.log("token:", token)
      document.getElementById("token").value = token
    })

    // refresh token every minute to prevent expiration
    setInterval(function(){
      grecaptcha.execute(site_key, {action: "homepage"}).then(function(token) {
        //console.log("refreshed token:", token)
        document.getElementById("token").value = token
      })
    }, 60000)

  })

})()
