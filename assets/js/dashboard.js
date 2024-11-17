document.addEventListener("DOMContentLoaded", () => {
    const accountList = document.querySelector(".account-list");

    if (accountList) {
        accountList.addEventListener("click", (event) => {
            if (event.target.classList.contains("btn-delete")) {
                event.preventDefault();

                const accountId = event.target.getAttribute("data-account-id");
                const accountElement = event.target.closest(".account-item");

                if (confirm("Êtes-vous sûr de vouloir supprimer ce compte ?")) {
                    fetch("delete_account.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: new URLSearchParams({ account_id: accountId }),
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.status === "success") {
                                alert(data.message);
                                accountElement.remove(); // Supprime l'élément du DOM
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch((error) => {
                            console.error("Erreur lors de la suppression :", error);
                            alert("Une erreur est survenue lors de la suppression.");
                        });
                }
            }
        });
    }
});
