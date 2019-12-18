jQuery(document).ready(function($) {
        console.log('works !!!');
        eraseCookie('antihacker_cookie');
        if(readCookie('antihacker_cookie') ==  null)
        {
          createCookie('antihacker_cookie', '1'); 
        }
        // work = readCookie('antihacker_cookie11');
        //console.log(work);
        function createCookie(name, value, days) {
            var expires;
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toGMTString();
            } else {
                expires = "";
            }
            document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
        }
        function readCookie(name) {
            var nameEQ = escape(name) + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return unescape(c.substring(nameEQ.length, c.length));
            }
            return null;
        }
        function eraseCookie(name) {
            createCookie(name, "", -1);
        }
});