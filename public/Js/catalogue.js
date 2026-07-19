document.addEventListener("DOMContentLoaded", function () {

    var cartToggle  = document.getElementById("cartToggle");
    var cartDrawer  = document.getElementById("cartDrawer");
    var cartOverlay = document.getElementById("cartOverlay");
    var cartClose   = document.getElementById("cartClose");
    var checkoutBtn = document.getElementById("checkoutBtn");

    function openCart() {
        if (cartDrawer)  cartDrawer.classList.add("open");
        if (cartOverlay) cartOverlay.classList.add("open");
        document.body.style.overflow = "hidden";
    }

    function closeCart() {
        if (cartDrawer)  cartDrawer.classList.remove("open");
        if (cartOverlay) cartOverlay.classList.remove("open");
        document.body.style.overflow = "";
    }

    if (cartToggle)  cartToggle.addEventListener("click", openCart);
    if (cartClose)   cartClose.addEventListener("click", closeCart);
    if (cartOverlay) cartOverlay.addEventListener("click", closeCart);

    // Checkout — vérifier si connecté
    if (checkoutBtn) {
        checkoutBtn.addEventListener("click", function () {
            var loggedIn = document.body.dataset.loggedIn === "1";
            if (!loggedIn) {
                alert("Veuillez vous connecter pour passer la commande.");
                window.location.href = "/PetMarket/views/layout/login.php";
            }
        });
    }

    // Fermer avec la touche Échap
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") closeCart();
    });

});