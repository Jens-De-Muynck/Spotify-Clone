$("#content").scroll(
    function () {
        let currentScrollPos = $("#content>header").offset().top;
        let heightSearchBar = $('main>header').height();
        let heightInfoBar = $("#content>header").height();
        let delta = heightSearchBar - heightInfoBar;

        if (currentScrollPos < 40) {
            $('main>header').addClass('schaduw');
        } else {
            $('main>header').removeClass('schaduw');
        }

        if (currentScrollPos < delta) {
            $("#content").addClass('isscrolled');
        } else {
            $("#content").removeClass('isscrolled');
        }




        console.log(currentScrollPos + " - " + heightSearchBar + " - " + heightInfoBar);
    }
);