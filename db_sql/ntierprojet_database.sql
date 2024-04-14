-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 06 avr. 2024 à 01:46
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `nitru`
--

-- --------------------------------------------------------

--
-- Structure de la table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `calorie_per_serving` float DEFAULT NULL,
  `serving_size` float DEFAULT NULL,
  `unity_of_measure` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nutritionist_client`
--

CREATE TABLE `nutritionist_client` (
  `client_id` int(11) NOT NULL,
  `nutritionist_id` int(11) NOT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `nutritionist_client`
--

INSERT INTO `nutritionist_client` (`client_id`, `nutritionist_id`, `modified_date`) VALUES
(41, 45, '2024-04-04 23:36:07'),
(42, 45, '2024-04-04 23:36:07'),
(43, 45, '2024-04-04 17:07:23'),
(47, 45, '2024-04-09 17:29:39');

-- --------------------------------------------------------

--
-- Structure de la table `nutri_requests`
--

CREATE TABLE `nutri_requests` (
  `id` int(8) NOT NULL,
  `nutri_id` int(8) NOT NULL,
  `etat` varchar(250) NOT NULL DEFAULT 'pending',
  `proof` varchar(250) NOT NULL DEFAULT 'nothing',
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `nutri_requests`
--

INSERT INTO `nutri_requests` (`id`, `nutri_id`, `etat`, `proof`, `created_date`) VALUES
(5, 41, 'pending', 'test', '2024-04-06');

-- --------------------------------------------------------

--
-- Structure de la table `plans`
--

CREATE TABLE `plans` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `period` int(11) DEFAULT NULL,
  `total_length` int(11) DEFAULT NULL,
  `median_caloric_value` float DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `plans`
--

INSERT INTO `plans` (`id`, `name`, `period`, `total_length`, `median_caloric_value`, `creator`, `type`) VALUES
(1, 'test1', 7, 28, NULL, 41, NULL),
(2, 'Test', 14, 30, 2200, 42, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `plan_recipes`
--

CREATE TABLE `plan_recipes` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `consumed` boolean NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pwdreset`
--

CREATE TABLE `pwdreset` (
  `pwdResetId` int(11) NOT NULL,
  `pwdResetEmail` text NOT NULL,
  `pwdResetSelector` text NOT NULL,
  `pwdResetToken` longtext NOT NULL,
  `pwdResetExpires` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `pwdreset`
--

INSERT INTO `pwdreset` (`pwdResetId`, `pwdResetEmail`, `pwdResetSelector`, `pwdResetToken`, `pwdResetExpires`) VALUES
(5, 'ahfqfqfg@gmail.com', 'd32637585e2a89fb', '$2y$10$ancCpDAVRIHXZTBUGr87.eRVl7fwyEkdiQwHkmZoxz7I1rdIMTJQu', '1705419153');

-- --------------------------------------------------------

--
-- Structure de la table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `calories` float DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `visibility` tinyint(1) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `creator` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `recipes`
--

INSERT INTO `recipes` (`id`, `name`, `calories`, `type`, `image_url`, `visibility`, `creation_date`, `creator`) VALUES
(1, 'Green Smoothie Bowl', 350, 'breakfast', 'Green-Smoothie.jpg', 1, '2024-01-27 00:00:00', 42),
(2, 'Quinoa Salad', 450, 'lunch', 'quinoa-salad.jpg', 1, '2024-01-27 00:00:00', 42),
(3, 'Grilled Salmon with Asparagus', 500, 'dinner', 'salmon.jpg', 1, '2024-01-27 00:00:00', 42),
(4, 'Avocado Toast', 250, 'breakfast', 'Avocado-Toast.jpg', 1, '2024-01-27 00:00:00', 42),
(5, 'Tomato Basil Soup', 175, 'lunch', 'Basilic_Soup.jpg', 1, '2024-01-27 00:00:00', 42),
(6, 'Chicken Caesar Salad', 400, 'lunch', 'grilled-chicken-caesar-salad-hero.jpg', 1, '2024-01-27 00:00:00', 42),
(7, 'Beef Stir Fry', 550, 'dinner', 'beef-stir-fry-01.jpg', 1, '2024-01-27 00:00:00', 42),
(9, 'Berry Yogurt Parfait', 220, 'snack', 'yogurt-parfait-4.jpg', 1, '2024-01-27 00:00:00', 42),
(10, 'Peanut Butter Banana Smoothie', 350, 'snack', 'Simply-Recipes-Peanut-Butter-Banana-Smoothie.jpg', 1, '2024-01-27 00:00:00', 41);


-- --------------------------------------------------------

--
-- Structure de la table `recipe_ingredient`
--

CREATE TABLE `recipe_ingredient` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `ingredient_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `active` int(11) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `age` int(11) DEFAULT NULL,
  `role` varchar(250) NOT NULL DEFAULT 'Regular',
  `height` int(11) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `daily_caloriegoal` int(11) DEFAULT NULL,
  `gender` varchar(250) DEFAULT NULL,
  `goal` varchar(250) DEFAULT NULL,
  `img` varchar(250) NOT NULL DEFAULT '/public/images/default-user.png',
  `total_clients` int(8) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `fullname`, `password`, `email`, `active`, `creation_date`, `age`, `role`, `height`, `weight`, `daily_caloriegoal`, `gender`, `goal`, `img`, `total_clients`) VALUES
(41, 'Ahmed Boulaabi', '$2y$10$7jAEIB7zXKP5M0uhP4ntuuVth5kOHFkjredT.Kfaq67a7AY6HvosO', 'ahmed@gmail.com', 1, '2024-01-27 00:00:00', 23, 'Regular', 178, 73, 227449, 'male', 'lose-weight-normal', '/public/images/default-user.png', 0),
(42, 'Admin', '$2y$10$Q5cdRJXVEp05oKUfefW6ZOh.meRN.UYM6/QR62NUw0Q0VoZRmQ1wa', 'admin@gmail.com', 1, '2024-01-27 00:00:00', 25, 'Admin', 185, 85, 355815, 'male', 'gain-weight-normal', '/public/images/default-user.png', 0),
(43, 'Wassim', '$2y$10$Q/DNapgswhvvrlDxFYTEteBbny7PgSIQ7PfQ2AZehY/z3aLqbfm1K', 'wassim.khedir@uha.fr', 1, '2024-03-16 15:02:08', 23, 'Nutritionist', 180, 65, 162324, 'male', 'lose-weight-fast', '/public/images/default-user.png', 0),
(45, 'nutri', '$2y$10$5pxxKYFxXT0pCVbNenfQUOF8osRtkirL/6bxxuWoUA.PFQmxSjWoe', 'nutri@gmailcom', 1, '2024-04-04 23:01:27', 23, 'Nutritionist', 178, 76, 333679, 'male', 'gain-weight-normal', '/public/images/default-user.png', 5),
(46, 'elias', '$2y$10$tVzrJ9maPOSD2NDGBAfDEeirVK67yogGhwTeGsdjtpTrUDmRRIG.i', 'elias@gmail.com', 1, '2024-04-05 17:27:58', 24, 'Regular', NULL, NULL, NULL, NULL, NULL, '/public/images/default-user.png', 0),
(47, 'mathias', '$2y$10$rTSjrEI/dmAcyuQhhxRJquc/6Mzlj670ZoC7O4T5XR32btW8EiLCS', 'mathias@gmail.com', 1, '2024-04-05 17:28:10', 22, 'Regular', NULL, NULL, NULL, NULL, NULL, '/public/images/default-user.png', 0);

-- --------------------------------------------------------

--
-- Structure de la table `user_plan`
--

CREATE TABLE `user_plan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `managed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_plan`
--

INSERT INTO `user_plan` (`id`, `user_id`, `plan_id`, `creation_date`, `modified_date`, `managed_by`) VALUES
(1, 41, 1, '2024-04-01 14:58:30', NULL, 45),
(2, 42, 2, '2024-04-01 14:58:30', NULL, 41);



CREATE TABLE `messages` (
    `id` int(11),
    `expediteur_id` int(11) NOT NULL,
    `destinataire_id` int(11) NOT NULL,
    `contenu` TEXT NOT NULL,
    `date_envoi` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `etat` int(3) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


--
-- Index pour les tables déchargées
--

--
-- Index pour la table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `notifications_ibfk1` (`sender_id`),
  ADD KEY `notifications_ibfk2` (`receiver_id`);

--
-- Index pour la table `nutritionist_client`
--
ALTER TABLE `nutritionist_client`
  ADD PRIMARY KEY (`client_id`,`nutritionist_id`);

--
-- Index pour la table `nutri_requests`
--
ALTER TABLE `nutri_requests`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator` (`creator`);

--
-- Index pour la table `plan_recipes`
--
ALTER TABLE `plan_recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Index pour la table `pwdreset`
--
ALTER TABLE `pwdreset`
  ADD PRIMARY KEY (`pwdResetId`);

--
-- Index pour la table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator` (`creator`);

--
-- Index pour la table `recipe_ingredient`
--
ALTER TABLE `recipe_ingredient`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user_plan`
--
ALTER TABLE `user_plan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `managed_by` (`managed_by`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `nutri_requests`
--
ALTER TABLE `nutri_requests`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `plan_recipes`
--
ALTER TABLE `plan_recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pwdreset`
--
ALTER TABLE `pwdreset`
  MODIFY `pwdResetId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT pour la table `user_plan`
--
ALTER TABLE `user_plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `nutritionist_client`
--
ALTER TABLE `nutritionist_client`
  ADD CONSTRAINT `nutritionist_client_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`);


--
-- Contraintes pour la table messages
--

ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`expediteur_id`) REFERENCES `users`(`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`destinataire_id`) REFERENCES `users`(`id`);

--
-- Contraintes pour la table `plans`
--
ALTER TABLE `plans`
  ADD CONSTRAINT `plans_ibfk_1` FOREIGN KEY (`creator`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `plan_recipes`
--
ALTER TABLE `plan_recipes`
  ADD CONSTRAINT `plan_recipes_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`),
  ADD CONSTRAINT `plan_recipes_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`);

--
-- Contraintes pour la table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`creator`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `recipe_ingredient`
--
ALTER TABLE `recipe_ingredient`
  ADD CONSTRAINT `recipe_ingredient_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`),
  ADD CONSTRAINT `recipe_ingredient_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`);

--
-- Contraintes pour la table `user_plan`
--
ALTER TABLE `user_plan`
  ADD CONSTRAINT `user_plan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_plan_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`),
  ADD CONSTRAINT `user_plan_ibfk_3` FOREIGN KEY (`managed_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
