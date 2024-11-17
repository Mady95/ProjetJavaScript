
CREATE DATABASE banking_app;

USE banking_app;

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_name` varchar(100) NOT NULL,
  `account_type` enum('courant','épargne') NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `accounts` (`id`, `user_id`, `account_name`, `account_type`, `balance`, `created_at`) VALUES
(1, 1, 'Compte Courant Alice', 'courant', 10.00, '2024-11-14 21:04:18'),
(2, 1, 'Compte Épargne Alice', 'épargne', 4000.00, '2024-11-14 21:04:18'),
(3, 2, 'Compte Courant Bob', 'courant', 200.00, '2024-11-14 21:04:18'),
(4, 1, 'Paypal', 'courant', 35.00, '2024-11-14 21:23:57');


CREATE TABLE `login_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `login_history` (`id`, `user_id`, `login_time`, `ip_address`) VALUES
(1, 1, '2024-11-15 15:48:01', '::1'),
(2, 1, '2024-11-15 15:57:51', '::1'),
(3, 1, '2024-11-15 16:00:40', '::1'),
(4, 1, '2024-11-15 16:03:27', '::1');


CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `transaction_type` enum('dépôt','retrait') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `transactions` (`id`, `account_id`, `transaction_type`, `amount`, `transaction_date`) VALUES
(1, 1, 'dépôt', 500.00, '2024-11-01 09:00:00'),
(2, 1, 'retrait', 200.00, '2024-11-05 13:30:00'),
(3, 2, 'dépôt', 1000.00, '2024-11-03 08:15:00'),
(4, 3, 'dépôt', 50.00, '2024-11-02 10:00:00'),
(5, 3, 'retrait', 20.00, '2024-11-06 14:45:00'),
(6, 4, 'dépôt', 45.00, '2024-11-14 21:36:47'),
(7, 4, 'dépôt', 45.00, '2024-11-14 21:36:47'),
(8, 2, 'retrait', 10.00, '2024-11-14 21:37:18'),
(9, 2, 'retrait', 10.00, '2024-11-14 21:37:18'),
(10, 4, 'dépôt', 10.00, '2024-11-14 21:43:00'),
(11, 4, 'dépôt', 10.00, '2024-11-14 21:43:00'),
(12, 4, 'dépôt', 20.00, '2024-11-14 21:46:50'),
(13, 1, 'retrait', 900.00, '2024-11-14 21:47:28'),
(14, 1, 'dépôt', 900.00, '2024-11-14 21:48:45'),
(15, 1, 'dépôt', 13.00, '2024-11-14 21:58:18'),
(16, 1, 'dépôt', 2.00, '2024-11-14 22:24:00'),
(17, 1, 'dépôt', 1.00, '2024-11-14 22:33:44'),
(18, 1, 'retrait', 6.00, '2024-11-14 22:37:22'),
(19, 4, 'dépôt', 200.00, '2024-11-14 23:13:58'),
(20, 2, 'retrait', 1000.00, '2024-11-14 23:46:06'),
(21, 1, 'dépôt', 1.00, '2024-11-15 14:09:48'),
(22, 1, 'retrait', 1.00, '2024-11-15 14:10:22'),
(23, 4, 'retrait', 300.00, '2024-11-15 14:21:38'),
(24, 2, 'dépôt', 20.00, '2024-11-15 14:27:12'),
(25, 4, 'dépôt', 5.00, '2024-11-15 14:41:08'),
(26, 1, 'retrait', 1500.00, '2024-11-15 14:41:18');


CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Alice', 'mady@test.fr', '$2y$10$DNQhgQ2uzqjOMgq0LqTth.dAxqI/c3WmyuPt9Bd/zZvYvP6Z6W2S6', '2024-11-14 20:52:11'),
(2, 'Alice Dupont', 'alice@example.com', '$2y$10$VjX3nZUkqxJhZrsIaKN6quxrQ.kWRAPf1P5.mOdEt3bDj7PIvGsyi', '2024-11-14 21:03:09'),
(3, 'Bob Martin', 'bob@example.com', '$2y$10$Kk49uF2T1mE1zjKYoh3tpO88FPwnNkJebHX2uh0pSMWEMuC/nAMQy', '2024-11-14 21:03:09');


ALTER TABLE `accounts` ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`);
ALTER TABLE `login_history` ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`);
ALTER TABLE `transactions` ADD PRIMARY KEY (`id`), ADD KEY `account_id` (`account_id`);
ALTER TABLE `users` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `email` (`email`);
ALTER TABLE `accounts`MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `login_history` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `transactions`MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `accounts` ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `login_history` ADD CONSTRAINT `login_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `transactions` ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`);

