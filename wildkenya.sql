-- ============================================================
-- WildKenya Database
-- BIT3208 Capstone Project
-- Virtual Safari Guide & Kenya Parks Info Portal
-- ============================================================

CREATE DATABASE IF NOT EXISTS wildkenya;
USE wildkenya;

-- ============================================================
-- TABLE 1: users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'tourist', 'guide') DEFAULT 'tourist',
    phone VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABLE 2: parks
-- ============================================================
CREATE TABLE IF NOT EXISTS parks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    county VARCHAR(100) NOT NULL,
    region VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    entry_fee_citizen DECIMAL(10,2) DEFAULT 0.00,
    entry_fee_resident DECIMAL(10,2) DEFAULT 0.00,
    entry_fee_nonresident DECIMAL(10,2) DEFAULT 0.00,
    best_season VARCHAR(150) DEFAULT NULL,
    size_km2 DECIMAL(10,2) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABLE 3: animals
-- ============================================================
CREATE TABLE IF NOT EXISTS animals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    scientific_name VARCHAR(150) DEFAULT NULL,
    description TEXT NOT NULL,
    conservation_status ENUM('Least Concern','Near Threatened','Vulnerable','Endangered','Critically Endangered') DEFAULT 'Least Concern',
    habitat VARCHAR(200) DEFAULT NULL,
    diet VARCHAR(150) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABLE 4: park_animals (links parks to animals)
-- ============================================================
CREATE TABLE IF NOT EXISTS park_animals (
    park_id INT NOT NULL,
    animal_id INT NOT NULL,
    PRIMARY KEY (park_id, animal_id),
    FOREIGN KEY (park_id) REFERENCES parks(id) ON DELETE CASCADE,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);

-- ============================================================
-- TABLE 5: guides
-- ============================================================
CREATE TABLE IF NOT EXISTS guides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    bio TEXT DEFAULT NULL,
    languages VARCHAR(200) DEFAULT NULL,
    specialization VARCHAR(200) DEFAULT NULL,
    price_per_day DECIMAL(10,2) DEFAULT 0.00,
    rating DECIMAL(3,2) DEFAULT 0.00,
    years_experience INT DEFAULT 0,
    certified TINYINT(1) DEFAULT 0,
    available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- TABLE 6: bookings
-- ============================================================
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tourist_id INT NOT NULL,
    guide_id INT DEFAULT NULL,
    park_id INT NOT NULL,
    booking_date DATE NOT NULL,
    duration_days INT DEFAULT 1,
    group_size INT DEFAULT 1,
    total_cost DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
    special_requests TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tourist_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (guide_id) REFERENCES guides(id) ON DELETE SET NULL,
    FOREIGN KEY (park_id) REFERENCES parks(id) ON DELETE CASCADE
);

-- ============================================================
-- TABLE 7: reviews
-- ============================================================
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    park_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (park_id) REFERENCES parks(id) ON DELETE CASCADE
);

-- ============================================================
-- SEED: Default Admin User
-- Email: admin@wildkenya.co.ke | Password: Admin@1234
-- ============================================================
INSERT INTO users (name, email, password, role) VALUES
('WildKenya Admin', 'admin@wildkenya.co.ke',
 '$2y$10$TKh8H1.PfbuSSvSgAoifuOICelm4ENBtRm0GS/x6HJlKTpNIxLlJO', 'admin');

-- ============================================================
-- SEED: 10 Kenya National Parks
-- ============================================================
INSERT INTO parks (name, county, region, description, entry_fee_citizen, entry_fee_resident, entry_fee_nonresident, best_season, size_km2, featured) VALUES

('Maasai Mara National Reserve', 'Narok', 'Rift Valley',
'Kenya\'s most famous wildlife reserve, home to the world-renowned Great Migration where over 1.5 million wildebeest, zebras, and gazelles cross the Mara River from Tanzania\'s Serengeti. The Mara offers exceptional big cat sightings — lion, leopard, and cheetah — across vast open savannah shared with the Maasai people. It is undoubtedly one of the greatest wildlife spectacles on Earth.',
800.00, 3500.00, 8900.00, 'July to October (Migration), January to February', 1510.00, 1),

('Amboseli National Park', 'Kajiado', 'Rift Valley',
'Famous worldwide for its enormous elephant herds roaming against the stunning backdrop of Mount Kilimanjaro — Africa\'s highest peak rising across the Tanzanian border. Amboseli offers Kenya\'s finest wildlife photography, especially at dawn when elephants parade through golden light below the snow-capped mountain. Swamps, open plains, and acacia woodland create a stunning variety of habitats.',
800.00, 3500.00, 8900.00, 'June to October, January to February', 392.00, 1),

('Tsavo East National Park', 'Taita-Taveta', 'Coast',
'One of the world\'s largest game reserves and Kenya\'s biggest national park, covering over 13,000 square kilometres of untamed wilderness. Famous for its iconic red elephants — coated in the region\'s distinctive red volcanic dust — and the Galana River which cuts through the otherwise arid landscape. Vast herds of buffalo, healthy lion populations, and far fewer tourist vehicles make this a true wilderness experience.',
800.00, 3500.00, 6600.00, 'June to September, January to February', 13747.00, 0),

('Tsavo West National Park', 'Taita-Taveta', 'Coast',
'Dramatic volcanic landscapes define Tsavo West — the Shetani Lava Flow, Roaring Rocks, and the magical Mzima Springs where crystal-clear water bubbles up from the ground, fed underground by Mount Kilimanjaro\'s glaciers. Hippos and crocodiles can be viewed through an underwater glass chamber at Mzima Springs. The park is an important sanctuary for black rhino and offers a lush, green contrast to Tsavo East.',
800.00, 3500.00, 6600.00, 'June to October', 9065.00, 0),

('Lake Nakuru National Park', 'Nakuru', 'Rift Valley',
'Built around the shores of the famous alkaline Lake Nakuru, this park became world-renowned for hosting millions of flamingos that turn its shoreline a breathtaking shade of pink. A designated rhino sanctuary, the park is one of Kenya\'s most reliable spots to see both black and white rhino. Lions, leopards, waterbuck, and the rare Rothschild giraffe also inhabit the acacia forests and open grasslands surrounding the lake.',
800.00, 3500.00, 6600.00, 'Year-round (flamingo numbers vary)', 188.00, 1),

('Hell\'s Gate National Park', 'Nakuru', 'Rift Valley',
'One of Kenya\'s most unique parks — visitors can freely walk, cycle, and rock-climb without a guide among buffalo, zebras, giraffes, and warthogs. Hell\'s Gate features towering volcanic cliffs, the dramatic Ol Njorowa Gorge, geothermal steam vents, and the iconic Fischer\'s Tower rock column. The landscape famously inspired the setting for Disney\'s The Lion King. The nearby Olkaria geothermal spa adds a unique extra experience.',
215.00, 1015.00, 3000.00, 'Year-round', 68.25, 0),

('Samburu National Reserve', 'Samburu', 'Northern Kenya',
'A remote and rugged reserve in Kenya\'s northern frontier, set along the life-giving Ewaso Ng\'iro River. Samburu is celebrated for the "Samburu Special Five" — species found only in northern Kenya: reticulated giraffe, Grevy\'s zebra, Beisa oryx, Somali ostrich, and gerenuk. Elephant families wade in the river daily, and the dry, rocky terrain creates dramatic scenery unlike any other Kenyan reserve.',
800.00, 3500.00, 7500.00, 'January to February, June to September', 165.00, 1),

('Aberdare National Park', 'Nyandarua', 'Central',
'A spectacular highland park perched on the Aberdare mountain range at altitudes exceeding 4,000 metres. Dramatically different from Kenya\'s savannah parks, Aberdare features dense montane forest, open moorland, and breathtaking waterfalls including Karuru Falls. It shelters the rare mountain bongo antelope, elusive black leopard, giant forest hog, and elephants that navigate the bamboo zones. The legendary Treetops Lodge here is where Princess Elizabeth became Queen in 1952.',
600.00, 3000.00, 6000.00, 'Year-round', 766.00, 0),

('Mount Kenya National Park', 'Nyeri', 'Central',
'Africa\'s second highest mountain and a UNESCO World Heritage Site, Mount Kenya National Park protects diverse ecosystems from lush montane forest and bamboo zones to alpine moorland and glaciers near the 5,199-metre summit. The park is home to elephant, leopard, giant forest hog, and numerous endemic species. A hike to Point Lenana (4,985m) is one of Kenya\'s great trekking challenges, rewarding with sunrise views above the clouds.',
600.00, 3000.00, 6000.00, 'January to March, July to October', 715.00, 0),

('Nairobi National Park', 'Nairobi', 'Central',
'The world\'s only national park sharing a boundary with a capital city — just 7 kilometres from Nairobi\'s Central Business District. The park\'s open plains, framed by the city skyline, create one of Africa\'s most surreal safari experiences. Home to lions, leopards, cheetahs, black rhino, buffalo, giraffes, and over 400 bird species. Also within easy reach are the David Sheldrick Wildlife Trust elephant nursery and the Giraffe Centre.',
430.00, 1000.00, 4500.00, 'Year-round', 117.21, 1);


-- ============================================================
-- SEED: 15 Kenya Wildlife Species
-- ============================================================
INSERT INTO animals (name, scientific_name, description, conservation_status, habitat, diet, featured) VALUES

('African Lion', 'Panthera leo',
'The apex predator of the African savannah and Africa\'s largest wild cat. Lions are the only truly social cats, living in prides of up to 30 individuals. Kenya\'s Maasai Mara is famous for its large lion prides and exceptional big cat sightings. Female lions do most of the hunting, targeting wildebeest, zebra, and buffalo cooperatively. A lion\'s roar can be heard up to 8 kilometres away and is used to communicate with pride members and warn off rivals.',
'Vulnerable', 'Open savannah, grassland, bushveld', 'Carnivore — wildebeest, zebra, buffalo, warthog', 1),

('African Elephant', 'Loxodonta africana',
'The world\'s largest land animal and the cornerstone of Kenya\'s conservation story. African elephants are highly intelligent and deeply social, living in family groups led by an experienced matriarch. Amboseli\'s large herds roaming below Kilimanjaro create one of Africa\'s most iconic images. Tsavo\'s elephants are famous for their distinctive red colouring from the volcanic dust. Kenya\'s elephant population has recovered significantly thanks to determined anti-poaching efforts.',
'Vulnerable', 'Savannah, forest, wetlands, bushland', 'Herbivore — grasses, leaves, bark, fruit', 1),

('Black Rhinoceros', 'Diceros bicornis',
'One of Africa\'s most endangered large mammals, hunted nearly to extinction for its horn. Kenya has led Africa in rhino conservation — the population has grown from fewer than 300 in the 1980s to over 900 today. Lake Nakuru National Park and Ol Pejeta Conservancy are key sanctuaries. The black rhino is a browser, smaller and more aggressive than the white rhino, using its pointed prehensile lip to grasp twigs and leaves. Spotting one is a truly special safari highlight.',
'Critically Endangered', 'Dense bush, thickets, semi-arid scrubland', 'Herbivore — leaves, branches, fruits, roots', 1),

('African Leopard', 'Panthera pardus pardus',
'The most elusive of Africa\'s big cats and arguably the most beautiful. A solitary, nocturnal predator, the leopard hauls its prey up into trees to keep it away from lions and hyenas — a remarkable feat of strength. Kenya\'s leopards are well-studied in the Maasai Mara and Laikipia. Spotting a leopard draped across a fever tree branch, dappled by sunlight filtering through the acacia canopy, is considered one of Kenya\'s greatest safari moments.',
'Vulnerable', 'Forest, savannah, rocky hills, riverine woodland', 'Carnivore — impala, baboon, warthog, dik-dik', 1),

('Plains Zebra', 'Equus quagga',
'The most common zebra species and one of Africa\'s most recognisable animals. Each zebra\'s stripe pattern is unique — like a fingerprint. Plains zebras are central to the Great Migration, forming the vanguard alongside wildebeest, their grazing patterns preparing the grass for smaller antelopes. Their stripes may confuse predators by making it difficult to single out an individual in a moving herd. Zebra stallions fiercely protect their family groups from lions and hyenas.',
'Near Threatened', 'Open grassland, savannah, light woodland', 'Herbivore — grasses, shrubs, herbs', 0),

('Wildebeest', 'Connochaetes taurinus',
'The star of the world-famous Great Migration — the greatest wildlife spectacle on Earth. Every year, 1.5 to 2 million wildebeest migrate in a 1,000-kilometre loop between Tanzania\'s Serengeti and Kenya\'s Maasai Mara, crossing crocodile-filled rivers in spectacular, chaotic scenes. Despite their ungainly appearance, wildebeest are powerful and fast. They form the prey base for most of the Mara\'s large predators.',
'Least Concern', 'Short-grass plains, open savannah', 'Herbivore — short grasses', 0),

('Cheetah', 'Acinonyx jubatus',
'The fastest land animal on Earth, capable of accelerating from 0 to 100 km/h in just three seconds and reaching top speeds of 120 km/h. Unlike other big cats, cheetahs hunt by day, relying on their extraordinary speed rather than stealth. Maasai Mara and Amboseli support important cheetah populations. Cheetahs are the most vulnerable of the big cats — unable to roar, they cannot defend their kills from lions and hyenas, and cub mortality is high.',
'Vulnerable', 'Open savannah, semi-arid plains, grassland', 'Carnivore — gazelle, impala, hare', 1),

('Masai Giraffe', 'Giraffa camelopardalis tippelskirchii',
'The tallest living animal on Earth — reaching 5.5 metres — and Kenya\'s most recognisable wildlife icon. The Masai giraffe is named after the Maasai people of Kenya and Tanzania, and is distinguished by its jagged, irregular chestnut patches. Despite their enormous size, giraffes are quiet and gentle animals. They use their 45-centimetre tongues to strip acacia leaves. The endangered Rothschild\'s giraffe subspecies can be visited at Nairobi\'s famous Giraffe Centre.',
'Vulnerable', 'Open woodland, savannah, bushland', 'Herbivore — acacia leaves, shoots, bark', 0),

('Hippopotamus', 'Hippopotamus amphibius',
'Despite their bulk and seemingly placid nature in the water, hippos are one of Africa\'s most dangerous animals. They spend up to 16 hours a day submerged in rivers and lakes to stay cool and protect their sensitive skin from the sun. At night they emerge to graze on short grasses, travelling up to 10 kilometres. The Mara River supports large hippo pods that can be watched safely from the banks on game drives. Hippos can run at 30 km/h on land.',
'Vulnerable', 'Rivers, lakes, wetlands adjacent to grassland', 'Herbivore — short grasses', 0),

('African Buffalo', 'Syncerus caffer',
'A member of Africa\'s famed Big Five and one of the continent\'s most formidable animals. Buffalo have never been domesticated and are considered highly unpredictable — old bulls expelled from herds ("dagga boys") are particularly dangerous. Buffalo live in large herds that provide safety in numbers against lion attacks — the herd will mount a coordinated defence to rescue a captured member. A keystone grazer, the buffalo maintains grassland ecosystems through heavy feeding.',
'Near Threatened', 'Savannah, forest, floodplains, semi-arid bush', 'Herbivore — grasses, reeds, aquatic plants', 0),

('Lesser Flamingo', 'Phoeniconaias minor',
'Kenya\'s Rift Valley lakes host one of the world\'s most spectacular flamingo concentrations. Up to two million lesser flamingos have been recorded at Lake Nakuru alone, turning the shoreline an extraordinary shade of shocking pink. Lesser flamingos feed on blue-green algae (cyanobacteria) in the alkaline lakes, filtering it from the water with their specially adapted downturned bills. Lake Bogoria and Lake Elementaita are also important Kenyan flamingo sites.',
'Near Threatened', 'Alkaline and saline lakes, coastal lagoons', 'Filter feeder — algae, cyanobacteria, diatoms', 0),

('Reticulated Giraffe', 'Giraffa reticulata',
'Found only in northern Kenya, Ethiopia, and Somalia, the reticulated giraffe is considered the most beautiful of all giraffe species — its large, clearly defined chestnut-brown polygonal patches are separated by a bold network of bright white lines, creating a striking mosaic pattern. Samburu National Reserve is one of the world\'s best places to see them. With fewer than 16,000 remaining in the wild, the reticulated giraffe is classified as Vulnerable.',
'Vulnerable', 'Open woodland, dry bushland, semi-arid savannah', 'Herbivore — acacia leaves, shrubs, flowers', 0),

('Grevy\'s Zebra', 'Equus grevyi',
'The largest and most endangered of the world\'s three zebra species, Grevy\'s zebra is restricted to northern Kenya and southern Ethiopia. It is instantly recognisable by its narrow, closely-spaced stripes, large rounded ears, and distinctive white belly. Unlike plains zebras, Grevy\'s have a more fluid, territorial social structure. Samburu National Reserve is one of the best places in the world to observe them. Fewer than 3,000 remain in the wild.',
'Endangered', 'Semi-arid grassland, thornbush, dry open savannah', 'Herbivore — grasses, forbs, bark', 0),

('African Wild Dog', 'Lycaon pictus',
'Africa\'s most endangered large carnivore and one of its most fascinating. Wild dogs hunt in highly coordinated packs with a hunting success rate of up to 80% — far higher than lions (30%) or leopards (38%). Their mottled coats are each uniquely patterned like a fingerprint. Once widespread across Africa, they have been pushed to the margins — Laikipia Plateau is now one of Kenya\'s best areas to see them. Each pack is a close-knit family unit with elaborate greeting ceremonies.',
'Endangered', 'Open savannah, open woodland, semi-arid areas', 'Carnivore — impala, gazelle, warthog, cane rat', 0),

('Mountain Bongo', 'Tragelaphus eurycerus isaaci',
'One of Africa\'s rarest and most beautiful antelopes — a deep chestnut-red forest antelope with vivid white vertical body stripes and long, spiralling horns present in both sexes. The mountain bongo exists only in Kenya\'s high-altitude forests, including the Aberdares and Mount Kenya. It is critically endangered with fewer than 100 individuals surviving in the wild. Conservation breeding programmes at private conservancies are working to reintroduce the bongo to its former forest range.',
'Critically Endangered', 'Dense montane forest, bamboo forest zones', 'Herbivore — leaves, grasses, roots, bark, fruits', 0);


-- ============================================================
-- SEED: Park-Animal Relationships
-- ============================================================

-- Maasai Mara (1): Lion, Elephant, Leopard, Cheetah, Zebra, Wildebeest, Hippo, Buffalo, Giraffe
INSERT INTO park_animals VALUES (1,1),(1,2),(1,4),(1,7),(1,5),(1,6),(1,9),(1,10),(1,8);

-- Amboseli (2): Elephant, Lion, Cheetah, Zebra, Wildebeest, Giraffe, Hippo, Buffalo
INSERT INTO park_animals VALUES (2,2),(2,1),(2,7),(2,5),(2,6),(2,8),(2,9),(2,10);

-- Tsavo East (3): Elephant, Lion, Leopard, Buffalo, Zebra, Hippo
INSERT INTO park_animals VALUES (3,2),(3,1),(3,4),(3,10),(3,5),(3,9);

-- Tsavo West (4): Black Rhino, Elephant, Lion, Leopard, Buffalo, Hippo
INSERT INTO park_animals VALUES (4,3),(4,2),(4,1),(4,4),(4,10),(4,9);

-- Lake Nakuru (5): Flamingo, Black Rhino, Lion, Leopard, Giraffe, Buffalo, Zebra
INSERT INTO park_animals VALUES (5,11),(5,3),(5,1),(5,4),(5,8),(5,10),(5,5);

-- Hell's Gate (6): Zebra, Buffalo, Giraffe
INSERT INTO park_animals VALUES (6,5),(6,10),(6,8);

-- Samburu (7): Reticulated Giraffe, Grevy's Zebra, Elephant, Lion, Leopard, Cheetah, Wild Dog
INSERT INTO park_animals VALUES (7,12),(7,13),(7,2),(7,1),(7,4),(7,7),(7,14);

-- Aberdare (8): Mountain Bongo, Leopard, Elephant, Buffalo
INSERT INTO park_animals VALUES (8,15),(8,4),(8,2),(8,10);

-- Mount Kenya (9): Elephant, Leopard, Buffalo, Mountain Bongo
INSERT INTO park_animals VALUES (9,2),(9,4),(9,10),(9,15);

-- Nairobi NP (10): Lion, Cheetah, Leopard, Black Rhino, Zebra, Giraffe, Buffalo, Wildebeest
INSERT INTO park_animals VALUES (10,1),(10,7),(10,4),(10,3),(10,5),(10,8),(10,10),(10,6);
