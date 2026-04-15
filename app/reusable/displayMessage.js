function displayMessage(type, message) {
    // Soo qabo divs-ka fariimaha
    const successDiv = $("#success");
    const errorDiv = $("#error");

    // Marka hore qari labadaba si aysan isku dul fuulin
    successDiv.addClass("hidden");
    errorDiv.addClass("hidden");

    if (type === "success") {
        successDiv.text(message).removeClass("hidden");
    } else {
        errorDiv.text(message).removeClass("hidden");
    }

    // Ikhtiyaari: Fariinta qari 4 ilbidhiqsi kadib
    setTimeout(() => {
        $(`#${type}`).addClass("hidden");
    }, 5000);
}