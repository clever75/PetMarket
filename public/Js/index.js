document.addEventListener("DOMContentLoaded", () => {
    const iconCart = document.querySelector(".card"); // sélectionne le panier
    const body = document.querySelector("body");
    const closeCart = document.querySelector(".close"); // bouton CLOSE dans le panier
    const checkoutBtn = document.querySelector(".checkOut");

    console.log(iconCart); // vérifier que ça renvoie l'élément

    iconCart.addEventListener("click", () => {
        body.classList.toggle("showCard");
    });

    closeCart.addEventListener("click", () => {
        body.classList.toggle("showCard");
    });
    console.log(iconCart)

    if (checkoutBtn) {
        checkoutBtn.addEventListener("click", (e) => {
            const loggedIn = body.dataset.loggedIn === "1";
            if (!loggedIn) {
                e.preventDefault();
                alert("Veuillez vous connecter pour payer.");
                const loginUrl = body.dataset.loginUrl || "/PetMarket/views/layout/login.php";
                window.location.href = loginUrl;
            }
        });
    }
});
