-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 16 fév. 2025 à 15:44
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
-- Base de données : `devwell_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`) VALUES
(77, 2, 3, 1);

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `name`, `description`) VALUES
(1, 'Santé', NULL),
(2, 'Productivité', NULL),
(3, 'Outils', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `created_at` datetime NOT NULL,
  `response` longtext DEFAULT NULL,
  `is_responded` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `contact`
--

INSERT INTO `contact` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `response`, `is_responded`) VALUES
(3, 'Tor', 'tor@gmail.com', 'test', 'test', '2024-12-24 15:21:00', 'Salut', 1),
(4, 'Ilyaas', 'il@gmail.com', 'TEST', 'TEST', '2025-02-02 16:22:26', 'YES', 1),
(5, 'Ilyaas', 'ilyaas@gmail.com', 'test', 'test', '2025-02-07 15:19:25', 'test', 1),
(8, 'Ilyaas', 'il@gmail.cim', 'dvsf', 'fs', '2025-02-10 10:22:55', NULL, 0),
(9, 'Ilyaas', 'ilyaas.95.jv@gmail.cim', 'SDFFGQF', 'sfrhghe', '2025-02-11 11:28:08', 'FSSFFDFDFDV', 1),
(16, 'Ilyaas', 'ilyaas@gmail.cim', 'Question ?', 'Vous avez quoi comment produits ?', '2025-02-14 07:27:47', 'Regarde nos catégories.', 1),
(17, 'Ilyaas', 'ilyaas20@gmail.cim', 'Bonjour', 'Bonjour, devwell !', '2025-02-15 09:40:54', 'Bonjour à toi !', 1),
(19, 'Ilyaas', 'ilyaas@gmail.cim', 'test', 'test', '2025-02-15 12:59:56', 'test validé', 1);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20241121080302', '2024-12-08 20:41:55', 95),
('DoctrineMigrations\\Version20241122142737', '2024-12-08 20:41:55', 353),
('DoctrineMigrations\\Version20241122160225', '2024-12-08 20:41:55', 88),
('DoctrineMigrations\\Version20241126152242', '2024-12-08 20:41:55', 17),
('DoctrineMigrations\\Version20241126201111', '2024-12-08 20:41:55', 231),
('DoctrineMigrations\\Version20241208180122', '2024-12-08 20:41:56', 9),
('DoctrineMigrations\\Version20241209084827', '2024-12-09 09:48:57', 47),
('DoctrineMigrations\\Version20241220084243', '2024-12-20 09:42:52', 472),
('DoctrineMigrations\\Version20241230102758', '2024-12-30 11:28:34', 2544);

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `created_at`) VALUES
(4, 1, 'livraison_en_cours', '2024-12-30 14:13:00'),
(33, 1, 'remboursée', '2025-02-14 07:35:00'),
(34, 37, 'livraison_en_cours', '2025-02-15 09:47:00');

-- --------------------------------------------------------

--
-- Structure de la table `order_item`
--

CREATE TABLE `order_item` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order_item`
--

INSERT INTO `order_item` (`id`, `product_id`, `order_id`, `quantity`, `price`) VALUES
(5, 1, 4, 2, 59.95),
(51, 3, 33, 1, 129.9),
(52, 1, 34, 1, 39.9),
(53, 2, 34, 1, 59.95),
(54, 3, 34, 4, 129.9),
(55, 4, 34, 1, 485),
(56, 8, 34, 10, 27.99);

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` double NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`id`, `category_id`, `name`, `price`, `stock`, `image`, `description`) VALUES
(1, 1, 'Lunette anti lumière bleu', 39.9, 10, '6756f6b24b374.webp', 'Les écrans attaquent sévèrement vos performances : Fatigue visuelle importante, impact sur le sommeil...\r\n\r\nChoisissez une filtration maximale de la lumière bleue pour une utilisation intense !'),
(2, 1, 'Coussin masseur cervicales autonome', 59.95, 10, '67b0b72faddfd.webp', 'Le cou(p) de génie pour soulager les tensions !\r\n\r\nLe shiatsu utilise les doigts et les paumes des mains pour exercer des pressions sur des points déterminés afin d\'améliorer la circulation de l\'énergie vitale appelée Qi.Ce masseur s\'inspire de cette pratique issue de la médecine chinoise traditionnelle.\r\nOn varie les plaisirs grâce au second mode de massage : des vibrations pour un massage plus dynamique.\r\nEncore mieux : on choisit le mode combiné pour ajouter les vibrations énergisantes au massage shiatsu profond.\r\nPensé pour être nomade, ce masseur cervicales autonome offre jusqu\'à 5 heures d\'autonomie.\r\nTrès doux au toucher, il est fabriqué à partir de mousse à mémoire de forme.Il dévoile une forme ergonomique et est muni d\'une sangle de réglage pour s\'adapter à toutes les morphologies.\r\n\r\n\r\nMembre du club ?Bénéficiez gratuitement d\'une extension de garantie de 2 ans.\r\n\r\n\r\n\r\nInformations complémentaires :\r\n\r\nAutonomie mode vibrations : 5 heures.\r\nAutonomie mode shiatsu : 4h30.\r\nAutonomie mode shiatsu + vibrations : 4 heures.\r\nArrêt automatique au bout de 15 minutes.\r\nTemps de charge : 4-5 heures.\r\n4 têtes de massage rotatives.\r\nContient : 1 coussin masseur avec télécommande, 1 chargeur et 1 notice.'),
(3, 1, 'EXCALIBUR Pro 120 Sac de frappe', 129.9, 10, '6756f863c025a.webp', 'Les points forts du sac de frappe EXCALIBUR PRO 120 en un coup d\'œil :\r\n- Matériau extérieur durable en similicuir\r\n- Fabriqué à la main\r\n- Coutures doubles\r\n- Remplissage professionnel en matériau textile\r\n- Fermeture éclair pour le remplissage supplémentaire\r\n- Suspension robuste avec coutures doubles et rivets\r\n- Suspension par chaîne avec mousqueton pivotant\r\n- Impression de design par sérigraphie\r\n\r\nSac de frappe EXCALIBUR PRO\r\n\r\nCe sac de frappe est extrêmement résistant pour un entraînement de boxe exigeant. Le sac de frappe EXCALIBUR PRO 120 répond à toutes les exigences d\'entraînement.\r\nQue vous recherchiez un sac de frappe pour la boxe, l\'entraînement professionnel, pour réduire le stress ou les frustrations, ou simplement comme alternative à votre entraînement habituel, ce sac de frappe répondra à toutes les situations d\'entraînement.\r\nLe matériau extérieur extrêmement résistant en similicuir de haute qualité et la suspension par chaîne chromée avec mousqueton pivotant font du sac de frappe EXCALIBUR PRO 120 votre partenaire d\'entraînement idéal. Avec une longueur d\'environ 120 cm et un diamètre d\'environ 35 cm, ce sac de frappe pèse environ 34 kg une fois rempli. Tout ce dont vous avez besoin en plus, ce sont des gants de boxe de haute qualité et l\'entraînement peut commencer.\r\n\r\nSac de frappe\r\nLe matériau extérieur noir du sac de frappe est en similicuir de haute qualité et extrêmement résistant. Il repousse la transpiration et est facile à nettoyer.\r\nLe sac de frappe est rempli de rembourrage textile sous haute pression, ce qui permet une absorption optimale des chocs et maintient une forme optimale du sac de frappe en permanence.\r\nLes quatre suspensions, directement fixées au sac de frappe avec des rivets massifs et une double couture, garantissent une tenue optimale et une suspension uniforme du sac de frappe.\r\n\r\nSuspension par chaîne\r\nLa chaîne quadruple chromée est composée de maillons massifs. Le mousqueton pivotant empêche la torsion de la suspension et réduit la charge sur la fixation murale ou au plafond. En plus de la chaîne, vous pouvez également trouver d\'autres accessoires de boxe chez nous.\r\n\r\nDimensions du sac de frappe\r\nHauteur du sac de frappe : environ 120 cm\r\nDiamètre du sac de frappe : environ 35 cm\r\nHauteur totale : environ 166 cm (comprenant la suspension par chaîne)\r\n\r\nPoids du sac de frappe\r\nPoids total, comprenant le remplissage : environ 34 kg\r\n\r\nEmplacement\r\nPour la suspension au plafond : Pour un entraînement optimal et sécurisé, un espace de 150 cm dans toutes les directions doit être disponible comme surface d\'entraînement.\r\nPour la suspension au mur : Pour un entraînement optimal et sécurisé, un espace de 150 cm à gauche, à droite et vers l\'avant doit être disponible comme surface d\'entraînement.\r\n\r\nDomaine d\'application\r\nIdéal pour la boxe à domicile et pour les clubs de boxe.'),
(4, 2, 'Chaise ergonomique de bureau - WAVE PREMIUM en mousse et maille', 485, 10, '6756fb798f7c1.webp', 'Pourquoi choisir la chaise ergonomique WAVE PREMIUM\r\nEncore plus confortable, la WAVE PREMIUM profite d’une conception Premium et de multiples réglages ergonomiques.\r\nChoisissez cette chaise premium pour :\r\n- Rembourrage haute densité de l’assise\r\n- Conception ergonomique et respectueuse de la morphologie humaine\r\n- Assise et dossiers ajustables (hauteur, profondeur, inclinaison)\r\n- Accoudoirs 4D rotatifs\r\n- Soutiens ergonomiques et réglables (support lombaire et appuie-tête)\r\n- Mécanisme synchrone supérieur tout en aluminium'),
(5, 2, 'Souris USB Filaire Souris Verticale Ergonomique', 11.48, 10, '67b0b3dda6053.webp', 'À propos de cet article\r\nSanté Le design de la souris ergonomique a été spécialement conçu pour éviter que le poignet et le bras ne subissent des dommages dus à une utilisation prolongée de la souris problèmes qui rencontrent les personnes qui utilisent les souris traditionnelles (syndrome RSI). Le design de la souris ergonomique rendra le mouvement plus lisse et flexible. Le choix idéal pour les personnes qui travaillent au quotidien et longtemps avec l\'ordinateur en naviguant sur Internet.\r\nCompatible souris ergonomique filaire compatible avec Windows 7, 8, 10, Windows XP, Vista, 2000 et Mac OS, Mac OS X, Linux. Technologie Plug and Play, compatible avec toutes les versions du système d\'exploitation.\r\nHaute précision Capteur optique laser de haute précision, souris optique en appuyant sur le bouton, vous pouvez passer du DPI de à 3200 – 2000 – 1200 – 800 ; répondent à chaque demande sur la vitesse de la souris, chaque niveau DPI a une LED couleur particulière. La technologie de suivi optique assure une plus grande sensibilité de la souris sur différents types de surfaces.\r\nFacile à utiliser : la souris verticale dispose de 5 boutons et d\'une molette 3D. Le bouton avant et arrière offre un maximum de confort dans la navigation sur Internet. C\'est une solution pratique pour toutes les personnes qui aiment utiliser le PC pour jouer ou qui travaillent sur l\'ordinateur pendant de nombreuses heures, touches faciles à commander et molette de défilement, longueur du câble 1,5 m.\r\nGarantie verticale de la souris WesKimed comprend le remboursement dans les 45 jours et qualité de 12 mois. Pour informations sur les produits, veuillez contacter le service client directement, nous répondrons à votre e-mail dans les 24 heures.'),
(6, 2, 'Clavier Logitech QWERTY Espagnol Sans-fil Ergo K860', 84.8, 10, '67b0b680dacd8.webp', 'Tout ce que vous avez toujours voulu savoir sur ce produit\r\nClavier Logitech QWERTY Sans-fil Ergo K860\r\nClavier Logitech QWERTY Espagnol Sans-fil Ergo K860\r\nCouleur : Noir\r\nModèle : Ergo K860\r\nClavier rétroéclairé : Non\r\nSans fil : Oui\r\nType et langue du clavier : QWERTY - Espagnol\r\nRétroéclairage : Non\r\nRétroéclairage RGB : Non\r\nConnecteur : Bluetooth\r\nType de touches : Mécanique\r\nPavé numérique : Non\r\nMarque : LOGITECH\r\nPoids : 2000 g'),
(7, 3, 'LG UltraWide™ 29WQ500-B.AEU Ecran PC Ultra Large 29\" - Dalle IPS résolution QHD (2560x1080), 5ms GtG 75Hz,sRGB99%, AMD FreeSync, inclinable', 157.62, 10, '67b0b430c1d7c.webp', 'À propos de cet article\r\nLa résolution QHD 21:9 UltraWide (2560x1080) offre 33 % d’espace d’écran en plus par rapport à un écran 16:9 Full HD, ainsi vous n\'avez plus besoin d\'utiliser les touches Alt+Tab pour afficher vos fenêtres côte à côte.\r\nLa dalle IPS avec 99% du sRVB offre une précision des couleurs exceptionnelle et un angle de vision plus large, ce qui permet d\'obtenir des couleurs haute fidélité pour reproduire des scènes encore plus vives sur le champ de bataille.\r\nLa technologie FreeSync vous assure une fluidité et une limpidité exceptionnelle des mouvements dans vos jeux rapides en haute résolution. Elle permet d\'éliminer pratiquement toutes les saccades et déchirures d\'écran.\r\nLes modes Flicker Safe et Lecture réduisent le scintillement et la lumière bleue qui peuvent provoquer une fatigue oculaire.\r\nLe HDR10 améliore la qualité de l\'image pour une expérience d\'immersion visuelle plus dynamique et des couleurs améliorées du contenu HDR'),
(8, 3, 'Glangeh Support Ordinateur Portable Aluminium, Refroidissement Support PC', 27.99, 10, '67b0b4fbe1608.webp', 'À propos de cet article\r\n[Ajustement Ergonomique] Ajustez facilement cet electronic device cooling pad à la hauteur et à l\'angle confortables qui conviennent à vos besoins, pour atteindre le niveau de vision parfait, ce qui réduit les douleurs au cou et aux épaules. Laissez-vous travailler confortablement à la maison/bureau/café/aéroport/extérieur. (Angle de réglage maximum 55°, hauteur correspondante 21cm)\r\n[Meilleur Refroidissement] La conception creuse du support laptop permet une meilleure circulation de l\'air pour une meilleure ventilation, offrant ainsi plus d\'espace pour évacuer la chaleur et empêchant votre ordinateur portable de surchauffer, protégeant ainsi mieux votre appareil.\r\n[Robuste et Stable] La conception à double pivot que nous avons utilisée est beaucoup plus stable qu\'un seul axe, ce qui permet de supporter jusqu\'à 8,8 lb (4kg) sans vaciller. Et le support ordinateur portable est composé d\'un alliage d\'aluminium épaissi et de coussinets en silicone antidérapants, assurez-vous que votre ordinateur portable est stable sur le support et évitez les rayures.\r\n[Portatif et Multifonctionnel] Le support PC portable est léger et compact une fois plié, vous pouvez facilement le mettre dans votre sac ou sac à dos pour ordinateur portable. Lors de l\'utilisation, vous pouvez également organiser vos articles de bureau sous le support, tels que le clavier, la souris, les écouteurs, etc. C\'est un excellent compagnon pour les voyages et les voyages d\'affaires.\r\n[Compatibilité Étendue] Glangeh support ordinateur portable s\'adapte à tout ordinateur portable de 10\" à 16\", compatible avec MacBook/Macbook Pro/MacBook Air/Lenovo ThinkPad/Asus/Acer et plus encore. C\'est un bon choix pour vous-même/collègues/famille/amis comme cadeau noël pratique.'),
(9, 3, 'Gimars XXL Tapis de Souris 3 en 1 avec Repose Poignet Clavier Ergonomique', 17.98, 10, '67b0b51aebeda.webp', 'À propos de cet article\r\n【XXL Desk Mat and Wrist Rest Set】Le grand tapis de souris mesure 800 x 305 mm, le repose-poignet pour clavier est : 432 x 86 x 26 mm, le repose-poignets de la souris est : 160 x 76 x 26 mm. Le grand tapis de souris a suffisamment d\'espace pour couvrir et protéger votre bureau, et vous offre également suffisamment d\'espace pour votre souris et votre clavier\r\n【Ergonomic Memory Foam Wrist Support Pad】 (Support de poignet ergonomique en mousse à mémoire de forme) Le repose-poignet de la souris adopte un design ergonomique à rainures pour mieux s\'adapter à votre main. Le repose-poignet est rempli de mousse à mémoire de forme, qui n\'est pas facile à déformer. Il soutient efficacement le poignet et maintient le poignet en ligne droite au lieu d\'un certain angle avec le clavier et la souris, ce qui peut soulager les douleurs liées à la frappe et le syndrome du canal carpien. Il convient à une utilisation à la maison, au bureau, dans les cafés Internet et à d\'autres occasions\r\n【Ultra-smooth, Breathable, Superfine Lycra Fabric & Water-resistant Surface】La surface du tapis de souris Gimars est faite d\'un tissu Lycra ultra-fin, qui est respirant et très agréable pour la peau. Il permet à la souris de se déplacer rapidement et assure un positionnement précis, adapté à toute souris. La surface imperméable peut facilement nettoyer l\'eau et les boissons renversées, et sécher rapidement, ce qui permet de garder votre bureau propre et bien rangé à tout moment\r\n【Base en caoutchouc naturel et antidérapant et bords cousus pour une utilisation durable】Le fond est fait de caoutchouc naturel et n\'a pas d\'odeur. La base en caoutchouc antidérapante du tapis de souris et du repose-poignets offre une forte adhérence pour éviter les glissements, et est compatible avec une variété de surfaces de bureau telles que le métal, le bois, le verre ou le plastique. Le tapis de souris gaming a un bord cousu délicat pour éviter l\'usure, la déformation et le décollement lors d\'une utilisation à long terme, ce qui le maintient à plat sur le bureau sans déformer les bords\r\n【Perfect Office Accessories for your staff, family and friend, 100% satisfaction for Purchasing】This mouse pad and keyboard wrist rest set is a wonderful desk accessory for office, working ,studying, it also can be a great choice for family and friend who works on a computer all day. Notre équipe d\'assistance vous répondra dans les 24 heures qui suivent votre achat. Si vous avez des problèmes, n\'hésitez pas à nous en faire part');

-- --------------------------------------------------------

--
-- Structure de la table `reset_password_request`
--

CREATE TABLE `reset_password_request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `selector` varchar(20) NOT NULL,
  `hashed_token` varchar(100) NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reset_password_request`
--

INSERT INTO `reset_password_request` (`id`, `user_id`, `selector`, `hashed_token`, `requested_at`, `expires_at`) VALUES
(12, 2, '7hnNOxKOFopI30IGUPFl', '+j9HnnP92VZYcmR9v4ZLajPbhHqLbW9F6C+IANZ2Kxo=', '2025-02-15 15:25:27', '2025-02-15 16:25:27'),
(13, 42, 'ZXQH3CQv4FCvzxcYh0RF', '1ykx/Iewoj79fzHpP5BST3CCVano+uKDteP+oHPslsQ=', '2025-02-15 15:25:49', '2025-02-15 16:25:49'),
(14, 1, '44OQcQSpUKMOgMWRaENS', 'M14FzVrC5F0oGCJ3NAZQ482sfiDdekt+Av0OPw4YOIY=', '2025-02-15 15:37:34', '2025-02-15 16:37:34');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `email`, `password`, `roles`) VALUES
(1, 'Ilyaas', 'AS', 'ilyaas@gmail.com', '$2y$13$QR82Cc.in1mOQsQ7WpNw1uKZ8TvzvPEeSPclSg.zqAQjBjeZrQ476', '[\"ROLE_ADMIN\"]'),
(2, 'user', 'user', 'user@gmail.com', '$2y$13$T/dYrZVMBEaKXbt/phD0NukXwpHQXpk9PMs4t5IqoETZBavg45CNC', '{\"1\":\"ROLE_USER\"}'),
(4, 'user', 'user', 'user3@gmail.com', '$2y$13$ND8YeGIYQfInhcJA312WFOlkhWJ0XI/hnxFOEKNzQaSGFBhAlUf8y', '[]'),
(6, 'Ilyaas', 'AS', 'ilyaas3@gmail.com', '$2y$13$/D4PoAs3BEt3uICmTAMn0eLILYTEb9vlGnzv7wJ1a71Aslbb4U3K.', '[]'),
(7, 'Ilyaas', 'AS', 'ilyaas4@gmail.com', '$2y$13$vBADmeNB.vgneL64sqdhpe1w/8o3c07aQ.3ouD/KkJzCF9Rv75nB6', '[]'),
(9, 'Ilyaas', 'ABDOUL AZIS', 'ilyaas6@gmail.com', '$2y$13$yJ0Z/PDa4jjDgaop2xRN8eNjGnPMITVvIvMKyfluQYPTkwSPKf9Jy', '[]'),
(11, 'Ilyaas', 'ABDOUL AZIS', 'ilyaas7@gmail.com', '$2y$13$uqq6ZQdwS76QqCFOPmFp0uSyXvJJ/NPPBJINWyUbIAXcsKeUAhNQq', '[]'),
(12, 'Ilyaas', 'AS', 'ilyaas8@gmail.com', '$2y$13$rbzr68iptbGRv6iS7w9KKuYd3cqfKTSWKyd3VZIDDdLVTQNfwiqR.', '[]'),
(37, 'Ilyaas', 'AS', 'ilyaas20@gmail.com', '$2y$13$124oA382mKH.8ONwCMAV3Ow7FKfa2cDko5dzgbyNtzLnXxhPwArR6', '[]'),
(38, 'Ilyaas', 'ABDOUL AZIS', 'ilyaas16@gmail.com', '$2y$13$Z3QyoRJORflv7WKXu4Wewe4cg7a1aYKoNihQnGL6UJRIzTh6CSKVW', '[]'),
(41, 'admin', 'admin', 'admin@gmail.com', '$2y$13$8bTWzMMvo6t9QYzc5HGoH.1hoe9mABSwXOgpYoUL6OKWTYcYWca0e', '[\"ROLE_USER\",\"ROLE_ADMIN\"]'),
(42, 'user', 'user', 'user2@gmail.com', '$2y$13$vO5NhEmfsTPK9rgLH71NxuW8b5yNVWRZPPm8A3Ks8pBvYJm9zPKEa', '[]'),
(43, 'user', 'user', 'user4@gmail.com', '$2y$13$WSGpAIFWZ/uNsKToDM7ZdOM6CMUS/u85lAxLngpOUtA2vSlC1sCJK', '[]'),
(45, 'Ilyaas', 'AS', 'ilyaas30@gmail.com', '$2y$13$z92P4ZIABHZGRiVuzMI.LuKG2w1X17xOJ3Nz/bzjbfipd.9tSzNvW', '[\"ROLE_USER\",\"ROLE_ADMIN\"]');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_BA388B7A76ED395` (`user_id`),
  ADD KEY `IDX_BA388B74584665A` (`product_id`);

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E52FFDEEA76ED395` (`user_id`);

--
-- Index pour la table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_52EA1F094584665A` (`product_id`),
  ADD KEY `IDX_52EA1F098D9F6D38` (`order_id`);

--
-- Index pour la table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_D34A04AD12469DE2` (`category_id`);

--
-- Index pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7CE748AA76ED395` (`user_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT pour la table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `FK_BA388B74584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `FK_BA388B7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `FK_E52FFDEEA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `FK_52EA1F094584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `FK_52EA1F098D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Contraintes pour la table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `FK_D34A04AD12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Contraintes pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
