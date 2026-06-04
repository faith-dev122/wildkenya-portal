-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2026 at 08:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wildkenya`
--

-- --------------------------------------------------------

--
-- Table structure for table `animals`
--

CREATE TABLE `animals` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `scientific_name` varchar(150) DEFAULT NULL,
  `description` text NOT NULL,
  `conservation_status` enum('Least Concern','Near Threatened','Vulnerable','Endangered','Critically Endangered') DEFAULT 'Least Concern',
  `habitat` varchar(200) DEFAULT NULL,
  `diet` varchar(150) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `animals`
--

INSERT INTO `animals` (`id`, `name`, `scientific_name`, `description`, `conservation_status`, `habitat`, `diet`, `image`, `featured`, `created_at`) VALUES
(1, 'African Lion', 'Panthera leo', 'The apex predator of the African savannah and Africa\'s largest wild cat. Lions are the only truly social cats, living in prides of up to 30 individuals. Kenya\'s Maasai Mara is famous for its large lion prides and exceptional big cat sightings. Female lions do most of the hunting, targeting wildebeest, zebra, and buffalo cooperatively. A lion\'s roar can be heard up to 8 kilometres away and is used to communicate with pride members and warn off rivals.', 'Vulnerable', 'Open savannah, grassland, bushveld', 'Carnivore — wildebeest, zebra, buffalo, warthog', NULL, 1, '2026-05-20 17:32:13'),
(2, 'African Elephant', 'Loxodonta africana', 'The world\'s largest land animal and the cornerstone of Kenya\'s conservation story. African elephants are highly intelligent and deeply social, living in family groups led by an experienced matriarch. Amboseli\'s large herds roaming below Kilimanjaro create one of Africa\'s most iconic images. Tsavo\'s elephants are famous for their distinctive red colouring from the volcanic dust. Kenya\'s elephant population has recovered significantly thanks to determined anti-poaching efforts.', 'Vulnerable', 'Savannah, forest, wetlands, bushland', 'Herbivore — grasses, leaves, bark, fruit', NULL, 1, '2026-05-20 17:32:13'),
(3, 'Black Rhinoceros', 'Diceros bicornis', 'One of Africa\'s most endangered large mammals, hunted nearly to extinction for its horn. Kenya has led Africa in rhino conservation — the population has grown from fewer than 300 in the 1980s to over 900 today. Lake Nakuru National Park and Ol Pejeta Conservancy are key sanctuaries. The black rhino is a browser, smaller and more aggressive than the white rhino, using its pointed prehensile lip to grasp twigs and leaves. Spotting one is a truly special safari highlight.', 'Critically Endangered', 'Dense bush, thickets, semi-arid scrubland', 'Herbivore — leaves, branches, fruits, roots', NULL, 1, '2026-05-20 17:32:13'),
(4, 'African Leopard', 'Panthera pardus pardus', 'The most elusive of Africa\'s big cats and arguably the most beautiful. A solitary, nocturnal predator, the leopard hauls its prey up into trees to keep it away from lions and hyenas — a remarkable feat of strength. Kenya\'s leopards are well-studied in the Maasai Mara and Laikipia. Spotting a leopard draped across a fever tree branch, dappled by sunlight filtering through the acacia canopy, is considered one of Kenya\'s greatest safari moments.', 'Vulnerable', 'Forest, savannah, rocky hills, riverine woodland', 'Carnivore — impala, baboon, warthog, dik-dik', NULL, 1, '2026-05-20 17:32:13'),
(5, 'Plains Zebra', 'Equus quagga', 'The most common zebra species and one of Africa\'s most recognisable animals. Each zebra\'s stripe pattern is unique — like a fingerprint. Plains zebras are central to the Great Migration, forming the vanguard alongside wildebeest, their grazing patterns preparing the grass for smaller antelopes. Their stripes may confuse predators by making it difficult to single out an individual in a moving herd. Zebra stallions fiercely protect their family groups from lions and hyenas.', 'Near Threatened', 'Open grassland, savannah, light woodland', 'Herbivore — grasses, shrubs, herbs', NULL, 0, '2026-05-20 17:32:13'),
(6, 'Wildebeest', 'Connochaetes taurinus', 'The star of the world-famous Great Migration — the greatest wildlife spectacle on Earth. Every year, 1.5 to 2 million wildebeest migrate in a 1,000-kilometre loop between Tanzania\'s Serengeti and Kenya\'s Maasai Mara, crossing crocodile-filled rivers in spectacular, chaotic scenes. Despite their ungainly appearance, wildebeest are powerful and fast. They form the prey base for most of the Mara\'s large predators.', 'Least Concern', 'Short-grass plains, open savannah', 'Herbivore — short grasses', NULL, 0, '2026-05-20 17:32:13'),
(7, 'Cheetah', 'Acinonyx jubatus', 'The fastest land animal on Earth, capable of accelerating from 0 to 100 km/h in just three seconds and reaching top speeds of 120 km/h. Unlike other big cats, cheetahs hunt by day, relying on their extraordinary speed rather than stealth. Maasai Mara and Amboseli support important cheetah populations. Cheetahs are the most vulnerable of the big cats — unable to roar, they cannot defend their kills from lions and hyenas, and cub mortality is high.', 'Vulnerable', 'Open savannah, semi-arid plains, grassland', 'Carnivore — gazelle, impala, hare', NULL, 1, '2026-05-20 17:32:13'),
(8, 'Masai Giraffe', 'Giraffa camelopardalis tippelskirchii', 'The tallest living animal on Earth — reaching 5.5 metres — and Kenya\'s most recognisable wildlife icon. The Masai giraffe is named after the Maasai people of Kenya and Tanzania, and is distinguished by its jagged, irregular chestnut patches. Despite their enormous size, giraffes are quiet and gentle animals. They use their 45-centimetre tongues to strip acacia leaves. The endangered Rothschild\'s giraffe subspecies can be visited at Nairobi\'s famous Giraffe Centre.', 'Vulnerable', 'Open woodland, savannah, bushland', 'Herbivore — acacia leaves, shoots, bark', NULL, 0, '2026-05-20 17:32:13'),
(9, 'Hippopotamus', 'Hippopotamus amphibius', 'Despite their bulk and seemingly placid nature in the water, hippos are one of Africa\'s most dangerous animals. They spend up to 16 hours a day submerged in rivers and lakes to stay cool and protect their sensitive skin from the sun. At night they emerge to graze on short grasses, travelling up to 10 kilometres. The Mara River supports large hippo pods that can be watched safely from the banks on game drives. Hippos can run at 30 km/h on land.', 'Vulnerable', 'Rivers, lakes, wetlands adjacent to grassland', 'Herbivore — short grasses', NULL, 0, '2026-05-20 17:32:13'),
(10, 'African Buffalo', 'Syncerus caffer', 'A member of Africa\'s famed Big Five and one of the continent\'s most formidable animals. Buffalo have never been domesticated and are considered highly unpredictable — old bulls expelled from herds (\"dagga boys\") are particularly dangerous. Buffalo live in large herds that provide safety in numbers against lion attacks — the herd will mount a coordinated defence to rescue a captured member. A keystone grazer, the buffalo maintains grassland ecosystems through heavy feeding.', 'Near Threatened', 'Savannah, forest, floodplains, semi-arid bush', 'Herbivore — grasses, reeds, aquatic plants', NULL, 0, '2026-05-20 17:32:13'),
(11, 'Lesser Flamingo', 'Phoeniconaias minor', 'Kenya\'s Rift Valley lakes host one of the world\'s most spectacular flamingo concentrations. Up to two million lesser flamingos have been recorded at Lake Nakuru alone, turning the shoreline an extraordinary shade of shocking pink. Lesser flamingos feed on blue-green algae (cyanobacteria) in the alkaline lakes, filtering it from the water with their specially adapted downturned bills. Lake Bogoria and Lake Elementaita are also important Kenyan flamingo sites.', 'Near Threatened', 'Alkaline and saline lakes, coastal lagoons', 'Filter feeder — algae, cyanobacteria, diatoms', NULL, 0, '2026-05-20 17:32:13'),
(12, 'Reticulated Giraffe', 'Giraffa reticulata', 'Found only in northern Kenya, Ethiopia, and Somalia, the reticulated giraffe is considered the most beautiful of all giraffe species — its large, clearly defined chestnut-brown polygonal patches are separated by a bold network of bright white lines, creating a striking mosaic pattern. Samburu National Reserve is one of the world\'s best places to see them. With fewer than 16,000 remaining in the wild, the reticulated giraffe is classified as Vulnerable.', 'Vulnerable', 'Open woodland, dry bushland, semi-arid savannah', 'Herbivore — acacia leaves, shrubs, flowers', NULL, 0, '2026-05-20 17:32:13'),
(13, 'Grevy\'s Zebra', 'Equus grevyi', 'The largest and most endangered of the world\'s three zebra species, Grevy\'s zebra is restricted to northern Kenya and southern Ethiopia. It is instantly recognisable by its narrow, closely-spaced stripes, large rounded ears, and distinctive white belly. Unlike plains zebras, Grevy\'s have a more fluid, territorial social structure. Samburu National Reserve is one of the best places in the world to observe them. Fewer than 3,000 remain in the wild.', 'Endangered', 'Semi-arid grassland, thornbush, dry open savannah', 'Herbivore — grasses, forbs, bark', NULL, 0, '2026-05-20 17:32:13'),
(14, 'African Wild Dog', 'Lycaon pictus', 'Africa\'s most endangered large carnivore and one of its most fascinating. Wild dogs hunt in highly coordinated packs with a hunting success rate of up to 80% — far higher than lions (30%) or leopards (38%). Their mottled coats are each uniquely patterned like a fingerprint. Once widespread across Africa, they have been pushed to the margins — Laikipia Plateau is now one of Kenya\'s best areas to see them. Each pack is a close-knit family unit with elaborate greeting ceremonies.', 'Endangered', 'Open savannah, open woodland, semi-arid areas', 'Carnivore — impala, gazelle, warthog, cane rat', NULL, 0, '2026-05-20 17:32:13'),
(15, 'Mountain Bongo', 'Tragelaphus eurycerus isaaci', 'One of Africa\'s rarest and most beautiful antelopes — a deep chestnut-red forest antelope with vivid white vertical body stripes and long, spiralling horns present in both sexes. The mountain bongo exists only in Kenya\'s high-altitude forests, including the Aberdares and Mount Kenya. It is critically endangered with fewer than 100 individuals surviving in the wild. Conservation breeding programmes at private conservancies are working to reintroduce the bongo to its former forest range.', 'Critically Endangered', 'Dense montane forest, bamboo forest zones', 'Herbivore — leaves, grasses, roots, bark, fruits', NULL, 0, '2026-05-20 17:32:13');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `tourist_id` int(11) NOT NULL,
  `guide_id` int(11) DEFAULT NULL,
  `park_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `duration_days` int(11) DEFAULT 1,
  `group_size` int(11) DEFAULT 1,
  `total_cost` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `special_requests` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `tourist_id`, `guide_id`, `park_id`, `booking_date`, `duration_days`, `group_size`, `total_cost`, `status`, `special_requests`, `created_at`) VALUES
(1, 3, NULL, 1, '2026-10-10', 5, 10, 40000.00, 'pending', '', '2026-05-27 21:35:18'),
(2, 2, NULL, 1, '2026-10-10', 5, 5, 20000.00, 'pending', '', '2026-05-27 21:36:45');

-- --------------------------------------------------------

--
-- Table structure for table `guides`
--

CREATE TABLE `guides` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bio` text DEFAULT NULL,
  `languages` varchar(200) DEFAULT NULL,
  `specialization` varchar(200) DEFAULT NULL,
  `price_per_day` decimal(10,2) DEFAULT 0.00,
  `rating` decimal(3,2) DEFAULT 0.00,
  `years_experience` int(11) DEFAULT 0,
  `certified` tinyint(1) DEFAULT 0,
  `available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parks`
--

CREATE TABLE `parks` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `county` varchar(100) NOT NULL,
  `region` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `entry_fee_citizen` decimal(10,2) DEFAULT 0.00,
  `entry_fee_resident` decimal(10,2) DEFAULT 0.00,
  `entry_fee_nonresident` decimal(10,2) DEFAULT 0.00,
  `best_season` varchar(150) DEFAULT NULL,
  `size_km2` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parks`
--

INSERT INTO `parks` (`id`, `name`, `county`, `region`, `description`, `entry_fee_citizen`, `entry_fee_resident`, `entry_fee_nonresident`, `best_season`, `size_km2`, `image`, `featured`, `created_at`) VALUES
(1, 'Maasai Mara National Reserve', 'Narok', 'Rift Valley', 'Kenya\'s most famous wildlife reserve, home to the world-renowned Great Migration where over 1.5 million wildebeest, zebras, and gazelles cross the Mara River from Tanzania\'s Serengeti. The Mara offers exceptional big cat sightings — lion, leopard, and cheetah — across vast open savannah shared with the Maasai people. It is undoubtedly one of the greatest wildlife spectacles on Earth.', 800.00, 3500.00, 8900.00, 'July to October (Migration), January to February', 1510.00, NULL, 1, '2026-05-20 17:32:13'),
(2, 'Amboseli National Park', 'Kajiado', 'Rift Valley', 'Famous worldwide for its enormous elephant herds roaming against the stunning backdrop of Mount Kilimanjaro — Africa\'s highest peak rising across the Tanzanian border. Amboseli offers Kenya\'s finest wildlife photography, especially at dawn when elephants parade through golden light below the snow-capped mountain. Swamps, open plains, and acacia woodland create a stunning variety of habitats.', 800.00, 3500.00, 8900.00, 'June to October, January to February', 392.00, NULL, 1, '2026-05-20 17:32:13'),
(3, 'Tsavo East National Park', 'Taita-Taveta', 'Coast', 'One of the world\'s largest game reserves and Kenya\'s biggest national park, covering over 13,000 square kilometres of untamed wilderness. Famous for its iconic red elephants — coated in the region\'s distinctive red volcanic dust — and the Galana River which cuts through the otherwise arid landscape. Vast herds of buffalo, healthy lion populations, and far fewer tourist vehicles make this a true wilderness experience.', 800.00, 3500.00, 6600.00, 'June to September, January to February', 13747.00, NULL, 0, '2026-05-20 17:32:13'),
(4, 'Tsavo West National Park', 'Taita-Taveta', 'Coast', 'Dramatic volcanic landscapes define Tsavo West — the Shetani Lava Flow, Roaring Rocks, and the magical Mzima Springs where crystal-clear water bubbles up from the ground, fed underground by Mount Kilimanjaro\'s glaciers. Hippos and crocodiles can be viewed through an underwater glass chamber at Mzima Springs. The park is an important sanctuary for black rhino and offers a lush, green contrast to Tsavo East.', 800.00, 3500.00, 6600.00, 'June to October', 9065.00, NULL, 0, '2026-05-20 17:32:13'),
(5, 'Lake Nakuru National Park', 'Nakuru', 'Rift Valley', 'Built around the shores of the famous alkaline Lake Nakuru, this park became world-renowned for hosting millions of flamingos that turn its shoreline a breathtaking shade of pink. A designated rhino sanctuary, the park is one of Kenya\'s most reliable spots to see both black and white rhino. Lions, leopards, waterbuck, and the rare Rothschild giraffe also inhabit the acacia forests and open grasslands surrounding the lake.', 800.00, 3500.00, 6600.00, 'Year-round (flamingo numbers vary)', 188.00, NULL, 1, '2026-05-20 17:32:13'),
(6, 'Hell\'s Gate National Park', 'Nakuru', 'Rift Valley', 'One of Kenya\'s most unique parks — visitors can freely walk, cycle, and rock-climb without a guide among buffalo, zebras, giraffes, and warthogs. Hell\'s Gate features towering volcanic cliffs, the dramatic Ol Njorowa Gorge, geothermal steam vents, and the iconic Fischer\'s Tower rock column. The landscape famously inspired the setting for Disney\'s The Lion King. The nearby Olkaria geothermal spa adds a unique extra experience.', 215.00, 1015.00, 3000.00, 'Year-round', 68.25, NULL, 0, '2026-05-20 17:32:13'),
(7, 'Samburu National Reserve', 'Samburu', 'Northern Kenya', 'A remote and rugged reserve in Kenya\'s northern frontier, set along the life-giving Ewaso Ng\'iro River. Samburu is celebrated for the \"Samburu Special Five\" — species found only in northern Kenya: reticulated giraffe, Grevy\'s zebra, Beisa oryx, Somali ostrich, and gerenuk. Elephant families wade in the river daily, and the dry, rocky terrain creates dramatic scenery unlike any other Kenyan reserve.', 800.00, 3500.00, 7500.00, 'January to February, June to September', 165.00, NULL, 1, '2026-05-20 17:32:13'),
(8, 'Aberdare National Park', 'Nyandarua', 'Central', 'A spectacular highland park perched on the Aberdare mountain range at altitudes exceeding 4,000 metres. Dramatically different from Kenya\'s savannah parks, Aberdare features dense montane forest, open moorland, and breathtaking waterfalls including Karuru Falls. It shelters the rare mountain bongo antelope, elusive black leopard, giant forest hog, and elephants that navigate the bamboo zones. The legendary Treetops Lodge here is where Princess Elizabeth became Queen in 1952.', 600.00, 3000.00, 6000.00, 'Year-round', 766.00, NULL, 0, '2026-05-20 17:32:13'),
(9, 'Mount Kenya National Park', 'Nyeri', 'Central', 'Africa\'s second highest mountain and a UNESCO World Heritage Site, Mount Kenya National Park protects diverse ecosystems from lush montane forest and bamboo zones to alpine moorland and glaciers near the 5,199-metre summit. The park is home to elephant, leopard, giant forest hog, and numerous endemic species. A hike to Point Lenana (4,985m) is one of Kenya\'s great trekking challenges, rewarding with sunrise views above the clouds.', 600.00, 3000.00, 6000.00, 'January to March, July to October', 715.00, NULL, 0, '2026-05-20 17:32:13'),
(10, 'Nairobi National Park', 'Nairobi', 'Central', 'The world\'s only national park sharing a boundary with a capital city — just 7 kilometres from Nairobi\'s Central Business District. The park\'s open plains, framed by the city skyline, create one of Africa\'s most surreal safari experiences. Home to lions, leopards, cheetahs, black rhino, buffalo, giraffes, and over 400 bird species. Also within easy reach are the David Sheldrick Wildlife Trust elephant nursery and the Giraffe Centre.', 430.00, 1000.00, 4500.00, 'Year-round', 117.21, NULL, 1, '2026-05-20 17:32:13'),
(11, 'Mount Elgon National Park', 'Bungoma', 'Rift Valley', 'The park surrounds part of Mt. Elgon, an extinct shield volcano and one of the oldest volcanic mountains in East Africa.', 500.00, 300.00, 800.00, 'June to August', 169.00, NULL, 1, '2026-05-27 21:08:57');

-- --------------------------------------------------------

--
-- Table structure for table `park_animals`
--

CREATE TABLE `park_animals` (
  `park_id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `park_animals`
--

INSERT INTO `park_animals` (`park_id`, `animal_id`) VALUES
(1, 1),
(1, 2),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(2, 1),
(2, 2),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(3, 1),
(3, 2),
(3, 4),
(3, 5),
(3, 9),
(3, 10),
(4, 1),
(4, 2),
(4, 3),
(4, 4),
(4, 9),
(4, 10),
(5, 1),
(5, 3),
(5, 4),
(5, 5),
(5, 8),
(5, 10),
(5, 11),
(6, 5),
(6, 8),
(6, 10),
(7, 1),
(7, 2),
(7, 4),
(7, 7),
(7, 12),
(7, 13),
(7, 14),
(8, 2),
(8, 4),
(8, 10),
(8, 15),
(9, 2),
(9, 4),
(9, 10),
(9, 15),
(10, 1),
(10, 3),
(10, 4),
(10, 5),
(10, 6),
(10, 7),
(10, 8),
(10, 10);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `park_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `park_id`, `rating`, `comment`, `created_at`) VALUES
(1, 2, 1, 5, 'The place is so beautiful and I enjoyed myself a lot. Definitely coming back. Thanks!!!', '2026-05-27 21:22:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','tourist','guide') DEFAULT 'tourist',
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `created_at`) VALUES
(1, 'WildKenya Admin', 'admin@wildkenya.co.ke', '$2y$10$fajELde8CbE1Rys5P3Cb7usB6eY8fFQCn5KtLwkYP8CWqEbs3RTIC', 'admin', NULL, '2026-05-20 17:32:13'),
(2, 'Faith Wanjiku', 'gichurefaith8@gmail.com', '$2y$10$6wE89ru38s8/otN75DnZVe0zhbKzPRI.XJWlGq1OKlppz6nz7l37a', 'tourist', '', '2026-05-20 20:05:20'),
(3, 'Moses Kamau', 'moseskamau123@gmail.com', '$2y$10$w9e6S1cgp4i.aywVqxxAk.hi5kb/XntuaJ7IAr8rzTPPZ794siS8i', 'guide', '+254 722220000', '2026-05-27 21:32:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `animals`
--
ALTER TABLE `animals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tourist_id` (`tourist_id`),
  ADD KEY `guide_id` (`guide_id`),
  ADD KEY `park_id` (`park_id`);

--
-- Indexes for table `guides`
--
ALTER TABLE `guides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `parks`
--
ALTER TABLE `parks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `park_animals`
--
ALTER TABLE `park_animals`
  ADD PRIMARY KEY (`park_id`,`animal_id`),
  ADD KEY `animal_id` (`animal_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `park_id` (`park_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `animals`
--
ALTER TABLE `animals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parks`
--
ALTER TABLE `parks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`tourist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`guide_id`) REFERENCES `guides` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`park_id`) REFERENCES `parks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guides`
--
ALTER TABLE `guides`
  ADD CONSTRAINT `guides_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `park_animals`
--
ALTER TABLE `park_animals`
  ADD CONSTRAINT `park_animals_ibfk_1` FOREIGN KEY (`park_id`) REFERENCES `parks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `park_animals_ibfk_2` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`park_id`) REFERENCES `parks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
