document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("transaction-form");
    const responseMessage = document.getElementById("response-message");
    const transactionHistory = document.getElementById("transaction-history");
    const accountSelect = document.getElementById("account_id");

    // Charger les comptes de l'utilisateur
    fetch("get_accounts.php")
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                // Éviter les doublons dans le menu déroulant
                accountSelect.innerHTML = '<option value="">-- Sélectionnez un compte --</option>';
                data.accounts.forEach(account => {
                    const option = document.createElement("option");
                    option.value = account.id;
                    option.textContent = `${account.account_name} - ${account.balance}€`;
                    accountSelect.appendChild(option);
                });
            }
        });
        

    // Soumission du formulaire via AJAX
    form.addEventListener("submit", (event) => {
        event.preventDefault();
        const formData = new FormData(form);

        // Désactiver le bouton pour éviter les soumissions multiples
        const submitButton = form.querySelector("button[type='submit']");
        submitButton.disabled = true;

        fetch("add_transaction.php", {
            method: "POST",
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    responseMessage.textContent = data.message;
                    responseMessage.style.color = "green";
                    updateTransactionHistory(formData.get("account_id"));
                } else {
                    responseMessage.textContent = data.message;
                    responseMessage.style.color = "red";
                }
                submitButton.disabled = false;
            })
            .catch(() => {
                responseMessage.textContent = "Une erreur est survenue.";
                responseMessage.style.color = "red";
                submitButton.disabled = false;
            });
    });

    // Mise à jour de l'historique des transactions
    function updateTransactionHistory(accountId) {
        fetch(`get_transactions.php?account_id=${accountId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    transactionHistory.innerHTML = `<table border="1" cellpadding="10">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.transactions
                                .map(
                                    (tx) =>
                                        `<tr>
                                            <td>${tx.transaction_date}</td>
                                            <td>${tx.transaction_type}</td>
                                            <td>${tx.amount}€</td>
                                        </tr>`
                                )
                                .join("")}
                        </tbody>
                    </table>`;
                } else {
                    transactionHistory.innerHTML = `<p>${data.message}</p>`;
                }
            });
    }
});
