-- Create tables
CREATE TABLE IF NOT EXISTS User(id INTEGER NOT NULL AUTO_INCREMENT, email VARCHAR(255) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, dietaryType VARCHAR(10), verified BOOLEAN DEFAULT FALSE, isAdmin BOOLEAN DEFAULT FALSE, createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS Profile(id INTEGER NOT NULL AUTO_INCREMENT, userId INTEGER NOT NULL, username VARCHAR(16) NOT NULL UNIQUE, avatar VARCHAR(255), type BOOLEAN DEFAULT FALSE, FOREIGN KEY (userId) REFERENCES User (id) ON DELETE CASCADE, createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS Ingredient(id INTEGER NOT NULL AUTO_INCREMENT, farmerId INTEGER NOT NULL, ingredient VARCHAR(40) NOT NULL, price DECIMAL(5,2) NOT NULL, unit VARCHAR(6) NOT NULL, thumbnail VARCHAR(255) NULL, unlisted BOOLEAN NOT NULL DEFAULT 0, FOREIGN KEY (farmerId) REFERENCES Profile (id) ON DELETE CASCADE, createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS Recipe(id INTEGER NOT NULL AUTO_INCREMENT, profileId INTEGER NOT NULL, title VARCHAR(100) NOT NULL, thumbnail VARCHAR(255), prepTime INTEGER DEFAULT 0, waitingTime INTEGER DEFAULT 0, servings INTEGER DEFAULT 0, public BOOLEAN DEFAULT FALSE, dietaryType VARCHAR(10), ingredients JSON NOT NULL DEFAULT '[]', instructions TEXT NOT NULL, FOREIGN KEY (profileId) REFERENCES Profile (id) ON DELETE CASCADE, createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS Cookbook(id INTEGER NOT NULL AUTO_INCREMENT, profileId INTEGER NOT NULL, title VARCHAR(100) NOT NULL, description TEXT NOT NULL, public BOOLEAN DEFAULT FALSE, thumbnail VARCHAR(255), FOREIGN KEY (profileId) REFERENCES Profile (id) ON DELETE CASCADE, createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS CookbookJoin(id INTEGER NOT NULL AUTO_INCREMENT, cookbookId INTEGER NOT NULL, recipeId INTEGER NOT NULL, FOREIGN KEY (cookbookId) REFERENCES Cookbook (id) ON DELETE CASCADE, FOREIGN KEY (recipeId) REFERENCES Recipe (id) ON DELETE CASCADE, createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS Comment(id INTEGER NOT NULL AUTO_INCREMENT, profileId INTEGER NOT NULL, recipeId INTEGER NOT NULL, content TEXT NOT NULL, rating INTEGER NOT NULL DEFAULT 0, FOREIGN KEY (profileId) REFERENCES Profile (id) ON DELETE CASCADE, FOREIGN KEY (recipeId) REFERENCES Recipe (id) ON DELETE CASCADE, createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS ResetToken(id INTEGER NOT NULL AUTO_INCREMENT, userId INTEGER NOT NULL, token VARCHAR(255) NOT NULL UNIQUE, expiresAt TIMESTAMP NOT NULL, FOREIGN KEY (userId) REFERENCES User (id) ON DELETE CASCADE, createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS Session(id INTEGER NOT NULL AUTO_INCREMENT, sessionId VARCHAR(255) NOT NULL, profileId INTEGER NOT NULL, FOREIGN KEY (profileId) REFERENCES Profile (id) ON DELETE CASCADE, createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS Purchase(id INTEGER NOT NULL AUTO_INCREMENT, farmerId INTEGER NOT NULL, ingredientId INTEGER NOT NULL, amount INTEGER NOT NULL, FOREIGN KEY (farmerId) REFERENCES Profile (id) ON DELETE CASCADE, FOREIGN KEY (ingredientId) REFERENCES Ingredient (id) ON DELETE CASCADE, createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS Invoice(id INTEGER NOT NULL AUTO_INCREMENT, invoiceId VARCHAR(32) NOT NULL, profileId INTEGER NOT NULL, purchaseIds JSON NOT NULL DEFAULT '[]', FOREIGN KEY (profileId) REFERENCES Profile (id) ON DELETE CASCADE, createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS Notification(id INTEGER NOT NULL AUTO_INCREMENT, senderId INTEGER NOT NULL, receiverId INTEGER NOT NULL, message VARCHAR(255) NOT NULL, link VARCHAR(255) NULL, isRead BOOLEAN NOT NULL DEFAULT 0, FOREIGN KEY (senderId) REFERENCES Profile (id) ON DELETE CASCADE, FOREIGN KEY (receiverId) REFERENCES Profile (id) ON DELETE CASCADE, createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id));

-- User Table
INSERT INTO User (email, password, dietaryType, verified, isAdmin) VALUES
('john_doe@example.com', '$2y$10$JzN47jNZdQGRwUT1e1Y7Ve1tjI83w4QH0SkgJhtzeF/PjOP3z9UB6', 'vegan', 0, 0),
('alice_smith@example.com', '$2y$10$uAtvT6rSwOZW4IHhaWXGauL08KbpWT6ZtyaTSicP09IHDhWj01KmS', 'halal', 0, 0),
('charlie_brown@example.com', '$2y$10$6LM6EtJZQgzk3RQ.BbfOEerC37MZO48UvWaVF1MS0KoaOig8uv6M6', 'vegetarian', 0, 0),
('emma_jones@example.com', '$2y$10$Hea7QbMbqXoRjqp8ucgAmO8Epxh6d9u4CKwuPZuFpPq0zH2AGvU4W', 'vegan', 0, 0),
('michael_lee@example.com', '$2y$10$IzH2mr2Eu.dfr99QH3y/IO5jcajTmzyEjoyvM8H1sUbiYT7UHiyfK', 'vegetarian', 0, 0),
('olivia_wilson@example.com', '$2y$10$dJOXp1NN5r9IibhePc64Xu1m45fPX4w5WBSFAzI8egT3NVqytAGKa', 'halal', 0, 0),
('lucas_martin@example.com', '$2y$10$IQso.Vjfzn0yoXJBGiQ7pu6hTNnKLJLA.sxlpO43RYh9BftPczJT2', 'vegan', 0, 0),
('isabella_clark@example.com', '$2y$10$p.BfOnQYEtj5ZodYySEmQufNjOQymKkja1oxA6xRiNC.uODTIS8DW', 'vegetarian', 0, 0),
('daniel_scott@example.com', '$2y$10$l.vxXE5.AZEJ676RUK/lruFBfpxRGHdELp2JbEtnphPYfeC6uAv4G', 'halal', 0, 0),
('sophia_davis@example.com', '$2y$10$VNjuEn.KByHUYimnvN6pReQAnF5JvXhv6wzRn3ZmbU/DgI/jpF1PC', 'vegetarian', 0, 0),
('aaron_thomas@example.com', '$2y$10$UBjAV93vqXDpl8z33HGZ6OjoF1y9Ih.8G70xg9AF4ybkXpp3qbWSS', 'vegan', 0, 0),
('grace_moore@example.com', '$2y$10$.Z9roGiHAYEGLIZbyoQ32.tNvRESkkY3gJJtqo/giMNkr4T8v1sea', 'halal', 0, 0),
('elijah_johnson@example.com', '$2y$10$gxHC8bJ73sdXa57zFBhyxO8nJ9MCMGCC0xBDKbShODB6c/NVYtcHy', 'vegetarian', 0, 0),
('chloe_white@example.com', '$2y$10$0A841YHsOke4XKxPlo/bgeXIxpCCpR5STxw7XebSmcW5fDBn0qQQS', 'vegan', 0, 0),
('noah_harris@example.com', '$2y$10$1kdKr.CBU0XPxY7TFyphE.ToTVUOsefx4WZlSa3h6rVuwiEvWQPDC', 'halal', 0, 0),
('ava_king@example.com', '$2y$10$hr.EF305hUBqtbEpf0OiveQYNCn8iT3Hl1B28mM/ZdlmvfaTJ94xW', 'vegetarian', 0, 0),
('liam_taylor@example.com', '$2y$10$hsOL/5h8tgllY04Wf/xRE.lUs1JKqAOyF1Um5JmvSx8ZQrbd9tnJC', 'halal', 0, 0),
('emma_lee@example.com', '$2y$10$nRZHA5de/ySKQp/l1FBsnu23Bt4IdEyGQc.VLBn7CL4GugwPqVMuO', 'vegan', 0, 0),
('mason_garcia@example.com', '$2y$10$sPqWqpbzhMsGqbEsadogFeK0L8h/ND08ezjw8tiBL6S/krWvkIzmC', 'vegetarian', 0, 0),
('oliver_hernandez@example.com', '$2y$10$BSYfMSeN32N8h60UPgmjZuel6OmCgv1Wi3w33TaeEA7FAEB44JYRu', 'halal', 1, 0),
('ava_martinez@example.com', '$2y$10$.eqrfM5HWCraMupgBCqze./H8EaQZ0g24uFYjwR0bu09mboFdNDhS', 'vegan', 0, 0),
('jackson_rodriquez@example.com', '$2y$10$y3/qHHblyG.9UrmxQpElOeiBDn1ZVqmg6J/uQqdsam2Yt3ySPuAf6', 'vegetarian', 0, 0),
('mia_walker@example.com', '$2y$10$w6x7oiesxcw2P7Y/u6lzN.SqkSpqueEo9DocQop3WCMv30yP7/bfq', 'halal', 0, 0),
('sebastian_lee@example.com', '$2y$10$pgfbqU9FLFN.R5S2mY4O6uOEOj9PG4im6VLje5uzYouGq767HkCr2', 'vegan', 0, 0),
('ella_anderson@example.com', '$2y$10$10U6hw6uKh0A.Zo0KTz7VuCrj1PVaoIGu709e8aF/nEAIhkZMea3K', 'vegetarian', 0, 0),
('grayson_thomas@example.com', '$2y$10$Psnj.12aRZ6rwyeiEjphr.tPNxTYI52gtCt9XwLhGyirXk0O5mC/e', 'halal', 0, 0),
('harper_gonzalez@example.com', '$2y$10$t9lfwUXsBREUoSi2d/SKuenErfcrGh.5xxtO3ASNvBCPlSPQXMqqa', 'vegan', 0, 0),
('elena_brown@example.com', '$2y$10$V6COs.hN8NdACuAtRr7ebe0OZ0XvDu6zs.LgekKbPBhKk7D3eX7He', 'vegetarian', 0, 0),
('lucas_jackson@example.com', '$2y$10$EttwANbcHjdkMpb5klTJnueoYchNxU3Uouu/5Gf.WZFQk/Ko2zyiq', 'halal', 0, 0),
('scarlett_wilson@example.com', '$2y$10$UvhTvgsDLYG6Qp8mycpbeO5uVXS7jju3Mo4C4Ja7C5sUQi8JFfWd.', 'vegetarian', 0, 0),
('benjamin_harris@example.com', '$2y$10$Bss2/3Wvwr3QF2OjZoFnIemUMFgxIRtxDnMulVqrUnr1A49tMcIl.', 'vegan', 0, 0),
('isabella_wilson@example.com', '$2y$10$aOPmC2piIiXQEATrMTAk1OGsNDFQ9hLiNXR4B58gF3Ad2TAnMPjWG', 'halal', 0, 0),
('william_martin@example.com', '$2y$10$nppVcrtKn.2spa0BV1EMh.8TDR2ZVs90KYT64ZJNMQF9aVQUdp9e6', 'vegetarian', 0, 0),
('madeline_jones@example.com', '$2y$10$zgu19GYAuq/9eVnh2rkG4ukQsLeD7GJO0G2DOFi9YgwhdhF9RJzWS', 'vegan', 0, 0),
('ethan_taylor@example.com', '$2y$10$jhSDenHAxvit9wZigL9uD.IrztyZ6I/kPCTwmgXCJ49AFIsRMCpF.', 'halal', 0, 0),
('samantha_clark@example.com', '$2y$10$PqeZReqeZWFfKgIpRezqauJFGpxGNALntFJ/V9QgpCe6YAfSNCVLe', 'vegetarian', 0, 0),
('matthew_lee@example.com', '$2y$10$sq47nqgQr3eUvFPeq.A2XeKCCaxSQvxLWk039FSgwBXbEOXWiDe4m', 'vegan', 0, 0),
('aiden_scott@example.com', '$2y$10$y4HYsAl0ueZZJMDLw99oye4sCMngKCYDsXOIrX078it6HcHWFD9nC', 'halal', 0, 0),
('lucy_garcia@example.com', '$2y$10$eCCB6p3wZ5pR3h.QGToTD.5NlFcx29kjmPOZI6OZ8Spifc.d0qZh6', 'vegetarian', 0, 0),
('james_hall@example.com', '$2y$10$vpRa50fXbhsaKpLBgQLRuO5JEfeNf43VBg.IM.92Kue7.70IYGM6C', 'vegan', 1, 0);

-- Profilea Table
INSERT INTO Profile (userId, username, avatar, type) VALUES
(1, 'john_doe', 'http://localhost/recipe-roots/public/uploads/avatars/john_avatar.jpg', 0),
(2, 'alice_smith', 'http://localhost/recipe-roots/public/uploads/avatars/alice_avatar.jpg', 0),
(3, 'charlie_brown', 'http://localhost/recipe-roots/public/uploads/avatars/charlie_avatar.jpg', 0),
(4, 'emma_jones', 'http://localhost/recipe-roots/public/uploads/avatars/emma_avatar.jpg', 0),
(5, 'michael_lee', 'http://localhost/recipe-roots/public/uploads/avatars/michael_avatar.jpg', 0),
(6, 'olivia_wilson', 'http://localhost/recipe-roots/public/uploads/avatars/olivia_avatar.jpg', 0),
(7, 'lucas_martin', 'http://localhost/recipe-roots/public/uploads/avatars/lucas_avatar.jpg', 0),
(8, 'isabella_clark', 'http://localhost/recipe-roots/public/uploads/avatars/isabella_avatar.jpg', 0),
(9, 'daniel_scott', 'http://localhost/recipe-roots/public/uploads/avatars/daniel_avatar.jpg', 0),
(10, 'sophia_davis', 'http://localhost/recipe-roots/public/uploads/avatars/sophia_avatar.jpg', 0),
(11, 'aaron_thomas', 'http://localhost/recipe-roots/public/uploads/avatars/aaron_avatar.jpg', 0),
(12, 'grace_moore', 'http://localhost/recipe-roots/public/uploads/avatars/grace_avatar.jpg', 0),
(13, 'elijah_johnson', 'http://localhost/recipe-roots/public/uploads/avatars/elijah_avatar.jpg', 0),
(14, 'chloe_white', 'http://localhost/recipe-roots/public/uploads/avatars/chloe_avatar.jpg', 0),
(15, 'noah_harris', 'http://localhost/recipe-roots/public/uploads/avatars/noah_avatar.jpg', 0),
(16, 'ava_king', 'http://localhost/recipe-roots/public/uploads/avatars/ava_avatar.jpg', 0),
(17, 'liam_taylor', 'http://localhost/recipe-roots/public/uploads/avatars/liam_avatar.jpg', 0),
(18, 'emma_lee', 'http://localhost/recipe-roots/public/uploads/avatars/emma_lee_avatar.jpg', 0),
(19, 'mason_garcia', 'http://localhost/recipe-roots/public/uploads/avatars/mason_avatar.jpeg', 0),
(20, 'oliver_hernandez', 'http://localhost/recipe-roots/public/uploads/avatars/oliver_avatar.jpg', 0),
(21, 'ava_martinez', 'http://localhost/recipe-roots/public/uploads/avatars/ava_martinez_avatar.jpg', 0),
(22, 'jack_rodriquez', 'http://localhost/recipe-roots/public/uploads/avatars/jackson_rodriquez_avatar.jpg', 0),
(23, 'mia_walker', 'http://localhost/recipe-roots/public/uploads/avatars/mia_walker_avatar.jpg', 0),
(24, 'sebastian_lee', 'http://localhost/recipe-roots/public/uploads/avatars/sebastian_lee_avatar.jpg', 0),
(25, 'ella_anderson', 'http://localhost/recipe-roots/public/uploads/avatars/ella_anderson_avatar.jpg', 0),
(26, 'grayson_thomas', 'http://localhost/recipe-roots/public/uploads/avatars/grayson_thomas_avatar.jpg', 0),
(27, 'harper_gonzalez', 'http://localhost/recipe-roots/public/uploads/avatars/harper_gonzalez_avatar.jpg', 0),
(28, 'elena_brown', 'http://localhost/recipe-roots/public/uploads/avatars/elena_brown_avatar.jpg', 0),
(29, 'lucas_jackson', 'http://localhost/recipe-roots/public/uploads/avatars/lucas_jackson_avatar.jpg', 0),
(30, 'scarlett_wilson', 'http://localhost/recipe-roots/public/uploads/avatars/scarlett_wilson_avatar.jpeg', 0),
(31, 'benjamin_harris', 'http://localhost/recipe-roots/public/uploads/avatars/benjamin_harris_avatar.jpg', 1),
(32, 'isabella_wilson', 'http://localhost/recipe-roots/public/uploads/avatars/isabella_wilson_avatar.jpg', 1),
(33, 'william_martin', 'http://localhost/recipe-roots/public/uploads/avatars/william_martin_avatar.jpg', 1),
(34, 'madeline_jones', 'http://localhost/recipe-roots/public/uploads/avatars/madeline_jones_avatar.jpg', 1),
(35, 'ethan_taylor', 'http://localhost/recipe-roots/public/uploads/avatars/ethan_taylor_avatar.jpg', 1),
(36, 'samantha_clark', 'http://localhost/recipe-roots/public/uploads/avatars/samantha_clark_avatar.jpg', 1),
(37, 'matthew_lee', 'http://localhost/recipe-roots/public/uploads/avatars/matthew_lee_avatar.jpg', 1),
(38, 'aiden_scott', 'http://localhost/recipe-roots/public/uploads/avatars/aiden_scott_avatar.jpg', 1),
(39, 'lucy_garcia', 'http://localhost/recipe-roots/public/uploads/avatars/lucy_garcia_avatar.jpg', 1),
(40, 'james_hall', 'http://localhost/recipe-roots/public/uploads/avatars/james_hall_avatar.jpg', 1);

-- Recipe Table
INSERT INTO Recipe (profileId, title, thumbnail, prepTime, waitingTime, servings, public, dietaryType, ingredients, instructions)
VALUES
(1, 'Classic Pancakes', 'http://localhost/recipe-roots/public/uploads/thumbnails/pancakes.jpeg', 10, 5, 4, 1, 'vegetarian', 
    '[{"ingredient": "Flour", "amount": 2, "unit": "cups"}, {"ingredient": "Milk", "amount": 1.5, "unit": "cups"}, {"ingredient": "Eggs", "amount": 2, "unit": "pcs"}]', 
    '1. In a large mixing bowl, sift the flour to remove any lumps and ensure it is light. 2. Gradually pour in the milk, whisking continuously to create a smooth batter. 3. Crack the eggs into a separate bowl and beat them until well-mixed, then slowly add them to the batter. 4. Heat a non-stick frying pan or griddle over medium heat, and lightly grease it with butter or oil. 5. Pour about 1/4 cup of batter onto the pan for each pancake, spreading it out into a circle. 6. Cook for about 2-3 minutes or until bubbles form on the surface, then flip the pancake over and cook for another 1-2 minutes until golden brown. 7. Repeat the process with the remaining batter, ensuring that the pan is lightly greased between each batch. 8. Serve the pancakes warm with syrup, fresh berries, or your favourite topping.'),

(2, 'Spaghetti Bolognese', 'http://localhost/recipe-roots/public/uploads/thumbnails/bolognese.jpg', 15, 30, 4, 1, 'halal', 
    '[{"ingredient": "Spaghetti", "amount": 500, "unit": "g"}, {"ingredient": "Minced Beef", "amount": 400, "unit": "g"}, {"ingredient": "Tomato Sauce", "amount": 1, "unit": "cups"}]', 
    '1. Bring a large pot of salted water to a boil over high heat. Add the spaghetti and cook according to the package instructions, typically 8-10 minutes for al dente pasta. Once cooked, drain the spaghetti, saving a cup of pasta water, and set it aside. 2. While the pasta is cooking, heat 1 tablespoon of olive oil in a large skillet over medium-high heat. 3. Add the minced beef to the pan, breaking it up with a spoon as it cooks. Continue cooking for 6-8 minutes until browned and no longer pink. 4. Add 1 cup of tomato sauce to the beef, stirring to combine, and lower the heat to a simmer. Let the sauce simmer for 15-20 minutes, allowing the flavors to meld together. Add salt, pepper, and any other desired herbs like oregano or basil to taste. 5. Once the sauce is ready, add the drained spaghetti into the skillet with the sauce, mixing until the pasta is well-coated. If the sauce is too thick, add a bit of the reserved pasta water to reach your desired consistency. 6. Serve the spaghetti hot, optionally garnishing with fresh grated Parmesan cheese and fresh basil leaves.'),

(3, 'Caesar Salad', 'http://localhost/recipe-roots/public/uploads/thumbnails/caesar_salad.jpg', 10, 0, 2, 1, NULL, 
    '[{"ingredient": "Lettuce", "amount": 1, "unit": "head"}, {"ingredient": "Croutons", "amount": 1, "unit": "cups"}, {"ingredient": "Caesar Dressing", "amount": 0.5, "unit": "cups"}]', 
    '1. Start by thoroughly washing the lettuce leaves to remove any dirt or residue. Pat them dry with paper towels or use a salad spinner to ensure they are crisp. 2. Tear the lettuce into bite-sized pieces and place them in a large salad bowl. 3. Add the croutons to the bowl, distributing them evenly over the lettuce. 4. Pour the Caesar dressing over the salad, using about 1/2 cup, or to taste. 5. Toss the salad gently but thoroughly to coat all of the ingredients with the dressing. 6. Optionally, you can garnish the salad with freshly grated Parmesan cheese or even some additional croutons for crunch. 7. Serve immediately, as Caesar salad is best when fresh.'),

(4, 'Vegetable Stir Fry', 'http://localhost/recipe-roots/public/uploads/thumbnails/stir_fry.jpg', 10, 10, 3, 1, 'vegan', 
    '[{"ingredient": "Mixed Vegetables", "amount": 300, "unit": "g"}, {"ingredient": "Soy Sauce", "amount": 2, "unit": "tbsp"}, {"ingredient": "Garlic", "amount": 1, "unit": "cloves"}]', 
    '1. Begin by preparing all of your vegetables. Wash, peel, and cut them into even-sized pieces to ensure even cooking. 2. Heat 1 tablespoon of oil in a wok or large frying pan over high heat until the oil begins to shimmer. 3. Add the minced garlic and stir-fry for 30 seconds or until it becomes fragrant. 4. Add the mixed vegetables to the pan. Stir-fry them for about 5-7 minutes, ensuring they are tender but still slightly crisp. Stir frequently to prevent burning. 5. Pour 2 tablespoons of soy sauce over the vegetables, stirring to evenly coat. 6. Allow the soy sauce to cook off for about 1 minute, then taste and adjust the seasoning as necessary. 7. Serve the stir fry hot over a bed of rice or noodles for a satisfying meal.'),

(5, 'Chocolate Chip Cookies', 'http://localhost/recipe-roots/public/uploads/thumbnails/cookies.jpg', 15, 20, 24, 1, 'vegetarian', 
    '[{"ingredient": "Butter", "amount": 1, "unit": "cups"}, {"ingredient": "Sugar", "amount": 1, "unit": "cups"}, {"ingredient": "Chocolate Chips", "amount": 1, "unit": "cups"}]', 
    '1. Preheat your oven to 180°C (350°F) and line a baking sheet with parchment paper. 2. In a large bowl, beat 1 cup of softened butter and 1 cup of sugar together using an electric mixer or by hand until the mixture becomes light and fluffy. 3. Gradually add 2 cups of all-purpose flour, mixing until the dough comes together. 4. Fold in 1 cup of chocolate chips until evenly distributed throughout the dough. 5. Roll the dough into small balls, about 1 inch in diameter, and place them about 2 inches apart on the prepared baking sheet. 6. Slightly flatten each dough ball with your fingers or a spoon to form cookie shapes. 7. Bake for 10-12 minutes, or until the edges of the cookies are golden brown. 8. Remove from the oven and let the cookies cool on the baking sheet for 5 minutes, then transfer to a wire rack to cool completely. 9. Store in an airtight container for up to a week.'),

(6, 'Grilled Chicken', 'http://localhost/recipe-roots/public/uploads/thumbnails/grilled_chicken.jpg', 10, 25, 4, 1, 'halal', 
    '[{"ingredient": "Chicken Breast", "amount": 4, "unit": "pcs"}, {"ingredient": "Olive Oil", "amount": 2, "unit": "tbsp"}, {"ingredient": "Spices", "amount": 1, "unit": "tbsp"}]', 
    '1. In a small bowl, combine 2 tablespoons of olive oil, 1 tablespoon of your preferred spices (such as paprika, garlic powder, and cumin), and a pinch of salt. Stir until the mixture forms a smooth marinade. 2. Coat the chicken breasts evenly with the marinade, ensuring all sides are covered. Let the chicken rest for at least 15 minutes to absorb the flavors. 3. Preheat your grill or grill pan to medium-high heat. 4. Place the chicken breasts on the grill and cook for 6-8 minutes per side, or until the internal temperature reaches 165°F (74°C). 5. Once cooked, remove the chicken from the grill and let it rest for 5 minutes before slicing. 6. Serve the grilled chicken with a side of vegetables or over a salad.'),

(7, 'Tomato Soup', 'http://localhost/recipe-roots/public/uploads/thumbnails/tomato_soup.jpg', 10, 20, 4, 1, NULL, 
    '[{"ingredient": "Tomatoes", "amount": 6, "unit": "pcs"}, {"ingredient": "Onion", "amount": 1, "unit": "pcs"}, {"ingredient": "Garlic", "amount": 2, "unit": "cloves"}]', 
    '1. Heat 1 tablespoon of olive oil in a large pot over medium heat. Once heated, add 1 chopped onion and 2 minced garlic cloves. Sauté until the onion becomes translucent, about 3-4 minutes. 2. Add 6 chopped tomatoes (fresh or canned) to the pot and cook for another 5-7 minutes until the tomatoes soften. 3. Using a blender, blend the soup until smooth. You can also use an immersion blender directly in the pot. 4. Return the blended soup to the pot, add a pinch of salt, pepper, and any desired herbs (like basil or thyme), and simmer for 10-15 minutes to allow the flavors to meld. 5. Serve the soup warm, optionally garnishing with a swirl of cream or fresh herbs.'),

(8, 'Beef Tacos', 'http://localhost/recipe-roots/public/uploads/thumbnails/beef_tacos.jpg', 15, 5, 6, 1, 'halal', 
    '[{"ingredient": "Taco Shells", "amount": 6, "unit": "pcs"}, {"ingredient": "Ground Beef", "amount": 500, "unit": "g"}, {"ingredient": "Cheese", "amount": 1, "unit": "cups"}]', 
    '1. Heat a skillet over medium heat and add 500g of ground beef. Cook the beef, breaking it apart with a spoon, until browned and fully cooked, about 6-8 minutes. 2. Add your favorite spices (cumin, chili powder, garlic powder) to the beef mixture and stir to combine. Continue cooking for an additional 2-3 minutes. 3. While the beef is cooking, warm 6 taco shells in the oven or microwave according to package instructions. 4. Once the beef is ready, fill each taco shell with the cooked beef, about 2-3 tablespoons per shell. 5. Sprinkle shredded cheese over the beef, then add optional toppings like lettuce, salsa, and sour cream. 6. Serve immediately, garnished with cilantro or hot sauce if desired.'),

(9, 'Veggie Pizza', 'http://localhost/recipe-roots/public/uploads/thumbnails/veggie_pizza.jpg', 20, 15, 8, 1, 'vegetarian', 
    '[{"ingredient": "Pizza Dough", "amount": 1, "unit": "pcs"}, {"ingredient": "Cheese", "amount": 1, "unit": "cups"}, {"ingredient": "Vegetables", "amount": 300, "unit": "g"}]', 
    '1. Preheat your oven to 200°C (400°F) and line a baking sheet with parchment paper. 2. Roll out the pizza dough on a lightly floured surface to your desired thickness, about 12 inches in diameter. 3. Spread 1/2 cup of pizza sauce or tomato sauce evenly over the base, leaving a small border around the edge for the crust. 4. Sprinkle 1 cup of shredded cheese evenly on top of the sauce. 5. Layer your desired vegetables, such as bell peppers, mushrooms, onions, and spinach, over the cheese. 6. Bake the pizza in the oven for 12-15 minutes or until the crust is golden and the cheese is bubbly and slightly browned. 7. Remove from the oven and let it cool slightly before slicing. Serve hot with a sprinkle of fresh basil.'),

(10, 'Fruit Smoothie', 'http://localhost/recipe-roots/public/uploads/thumbnails/smoothie.jpg', 5, 0, 2, 1, 'vegan', 
    '[{"ingredient": "Banana", "amount": 1, "unit": "pcs"}, {"ingredient": "Milk", "amount": 1, "unit": "cups"}, {"ingredient": "Berries", "amount": 1, "unit": "cups"}]', 
    '1. Peel and slice 1 ripe banana into small pieces, then place it into a blender. 2. Add 1 cup of your preferred milk (dairy or plant-based) into the blender. 3. Add 1 cup of mixed berries (fresh or frozen) to the blender. 4. Blend everything on high until smooth and creamy. If the smoothie is too thick, add a splash more milk. 5. Pour into glasses and serve immediately, optionally garnished with extra berries or a sprig of mint.'),

(11, 'Avocado Toast', 'http://localhost/recipe-roots/public/uploads/thumbnails/avocado_toast.jpg', 5, 0, 1, 1, NULL, 
    '[{"ingredient": "Bread", "amount": 2, "unit": "slices"}, {"ingredient": "Avocado", "amount": 1, "unit": "lb"}, {"ingredient": "Salt", "amount": 0.5, "unit": "tsp"}]', 
    '1. Toast 2 slices of your favorite bread (sourdough or whole-grain works well) until golden brown and crisp. 2. While the bread is toasting, slice 1 ripe avocado in half, remove the pit, and scoop the flesh into a small bowl. Mash the avocado with a fork until smooth or leave it chunky if you prefer. 3. Spread the mashed avocado generously onto the warm toasted bread. 4. Sprinkle with 1/2 teaspoon of salt and optionally add black pepper, chili flakes, or a squeeze of lemon juice for extra flavor. 5. Serve immediately, optionally garnished with fresh herbs or a fried egg on top.'),

(12, 'Beef Stir Fry', 'http://localhost/recipe-roots/public/uploads/thumbnails/beef_stir_fry.jpg', 15, 10, 3, 1, 'halal', 
    '[{"ingredient": "Beef", "amount": 300, "unit": "g"}, {"ingredient": "Soy Sauce", "amount": 2, "unit": "tbsp"}, {"ingredient": "Garlic", "amount": 1, "unit": "cloves"}]', 
    '1. Slice the beef into thin strips and season with 1 tablespoon of soy sauce and black pepper. Let it marinate for about 5 minutes. 2. Heat 1 tablespoon of vegetable oil in a large pan or wok over medium-high heat. 3. Add the marinated beef to the pan and stir-fry for 2-3 minutes until it is browned and cooked through. 4. Add 1 chopped garlic clove and stir-fry for another 30 seconds until fragrant. 5. Add 1 tablespoon of soy sauce, mix well, and cook for 2 more minutes. 6. Serve the beef stir fry over steamed rice, garnished with chopped green onions.'),

(13, 'Grilled Cheese Sandwich', 'http://localhost/recipe-roots/public/uploads/thumbnails/grilled_cheese.jpg', 5, 5, 2, 1, NULL, 
    '[{"ingredient": "Bread", "amount": 2, "unit": "slices"}, {"ingredient": "Cheese", "amount": 2, "unit": "slices"}]', 
    '1. Heat a pan over medium heat. 2. Place 2 slices of bread on a clean surface and layer 1 slice of cheese between the slices of bread. 3. Butter the outside of the bread lightly, making sure to cover each side evenly. 4. Place the sandwich in the hot pan and grill for 2-3 minutes per side, or until the bread is golden brown and the cheese is melted. 5. Remove from the pan, slice diagonally, and serve hot.'),

(14, 'Chicken Curry', 'http://localhost/recipe-roots/public/uploads/thumbnails/chicken_curry.jpg', 15, 30, 4, 1, 'halal', 
    '[{"ingredient": "Chicken", "amount": 500, "unit": "g"}, {"ingredient": "Curry Powder", "amount": 1, "unit": "tbsp"}, {"ingredient": "Coconut Milk", "amount": 1, "unit": "cups"}]', 
    '1. In a large pan, heat 2 tablespoons of oil over medium heat. 2. Add 500g of chicken pieces and cook until they are browned on all sides. 3. Stir in 1 tablespoon of curry powder and cook for 1 minute to release the flavors. 4. Pour in 1 cup of coconut milk and stir well. 5. Bring to a simmer and cook for 20 minutes, or until the chicken is cooked through and tender. 6. Serve the curry with rice or flatbread, garnished with fresh cilantro.'),

(15, 'Lemonade', 'http://localhost/recipe-roots/public/uploads/thumbnails/lemonade.jpg', 5, 0, 4, 1, 'vegan', 
    '[{"ingredient": "Lemon", "amount": 4, "unit": "pcs"}, {"ingredient": "Sugar", "amount": 1, "unit": "cups"}, {"ingredient": "Water", "amount": 5, "unit": "cups"}]', 
    '1. Squeeze the juice from 4 fresh lemons into a pitcher. 2. Add 1 cup of sugar to the lemon juice and stir until dissolved. 3. Add 5 cups of cold water to the pitcher and mix well. 4. Taste the lemonade and adjust the sweetness by adding more sugar if desired. 5. Serve over ice, optionally garnished with lemon slices or mint leaves.'),

(16, 'Mushroom Risotto', 'http://localhost/recipe-roots/public/uploads/thumbnails/mushroom_risotto.jpg', 10, 30, 4, 1, 'vegetarian', 
    '[{"ingredient": "Arborio Rice", "amount": 1, "unit": "cups"}, {"ingredient": "Mushrooms", "amount": 200, "unit": "g"}, {"ingredient": "Vegetable Broth", "amount": 4, "unit": "cups"}]', 
    '1. Heat 1 tablespoon of olive oil in a large pan over medium heat. 2. Add 200g of sliced mushrooms and cook for 5-7 minutes until they release their moisture and become tender. 3. Stir in 1 cup of Arborio rice, and cook for 2 minutes, allowing the rice to lightly toast. 4. Gradually add 1/2 cup of vegetable broth at a time, stirring frequently, and allowing the liquid to be absorbed before adding more broth. Continue this process until all 4 cups of broth are absorbed and the rice is tender and creamy (about 20-25 minutes). 5. Once the rice is cooked, stir in 1/4 cup of grated Parmesan cheese and a pinch of salt and pepper. 6. Serve the risotto hot, garnished with fresh herbs like parsley or thyme.'),

(17, 'Pasta Primavera', 'http://localhost/recipe-roots/public/uploads/thumbnails/pasta_primavera.jpg', 10, 15, 4, 1, 'vegetarian', 
    '[{"ingredient": "Pasta", "amount": 300, "unit": "g"}, {"ingredient": "Bell Peppers", "amount": 2, "unit": "pcs"}, {"ingredient": "Zucchini", "amount": 1, "unit": "pcs"}]', 
    '1. Cook 300g of pasta in a large pot of salted water according to package instructions. Drain the pasta and set aside, reserving a cup of pasta water. 2. Heat 2 tablespoons of olive oil in a large pan over medium heat. 3. Slice 2 bell peppers and 1 zucchini into thin strips, and add them to the pan. Sauté the vegetables for 5-7 minutes until they are tender but still crisp. 4. Add the cooked pasta to the pan with the vegetables, tossing to combine. If needed, add some reserved pasta water to make a light sauce. 5. Season with salt, pepper, and your choice of herbs like basil or oregano. 6. Serve the pasta with a sprinkle of Parmesan cheese and fresh basil on top.'),

(18, 'Fish Tacos', 'http://localhost/recipe-roots/public/uploads/thumbnails/fish_tacos.jpg', 10, 10, 4, 1, 'halal', 
    '[{"ingredient": "Fish Fillets", "amount": 400, "unit": "g"}, {"ingredient": "Taco Shells", "amount": 4, "unit": "pcs"}, {"ingredient": "Cabbage", "amount": 100, "unit": "g"}]', 
    '1. Heat 1 tablespoon of olive oil in a skillet over medium heat. 2. Season 400g of fish fillets (such as tilapia or cod) with salt, pepper, and any desired spices (cumin, paprika). 3. Cook the fish in the skillet for 3-4 minutes per side until golden and fully cooked. 4. While the fish is cooking, shred 100g of cabbage and prepare any desired toppings, such as salsa, avocado, or sour cream. 5. Warm the taco shells in the oven according to package instructions. 6. Flake the fish into bite-sized pieces and distribute it among the taco shells. Top with the shredded cabbage and any other toppings. 7. Serve immediately with a wedge of lime for squeezing over the tacos.'),

(19, 'Sweet Potato Fries', 'http://localhost/recipe-roots/public/uploads/thumbnails/sweet_potato_fries.jpg', 10, 25, 4, 1, 'vegan', 
    '[{"ingredient": "Sweet Potatoes", "amount": 4, "unit": "pcs"}, {"ingredient": "Olive Oil", "amount": 2, "unit": "tbsp"}, {"ingredient": "Paprika", "amount": 1, "unit": "tsp"}]', 
    '1. Preheat your oven to 220°C (425°F) and line a baking sheet with parchment paper. 2. Peel 4 sweet potatoes and cut them into evenly sized fries. 3. In a bowl, toss the sweet potato fries with 2 tablespoons of olive oil, 1 teaspoon of paprika, and a pinch of salt. 4. Spread the fries in a single layer on the baking sheet. 5. Bake for 20-25 minutes, flipping the fries halfway through, until they are crispy and golden brown. 6. Serve immediately with your favourite dipping sauce or as a side to your main dish.'),

(20, 'Apple Crisp', 'http://localhost/recipe-roots/public/uploads/thumbnails/apple_crisp.jpg', 15, 40, 6, 1, 'vegetarian', 
    '[{"ingredient": "Apples", "amount": 6, "unit": "pcs"}, {"ingredient": "Oats", "amount": 1, "unit": "cups"}, {"ingredient": "Butter", "amount": 0.5, "unit": "cups"}]', 
    '1. Preheat your oven to 180°C (350°F) and grease a baking dish with butter. 2. Peel, core, and slice 6 apples into thin wedges, then place them into the prepared dish. 3. In a bowl, combine 1 cup of oats, 1/2 cup of butter (melted), 1/2 cup of brown sugar, and 1/2 teaspoon of cinnamon. Mix until the ingredients form a crumbly topping. 4. Spread the oat mixture evenly over the apples in the baking dish. 5. Bake for 35-40 minutes, or until the topping is golden and the apples are tender. 6. Serve the apple crisp warm with a scoop of vanilla ice cream or whipped cream.');

-- Additional data for Recipe Table
INSERT INTO Recipe (profileId, title, thumbnail, prepTime, waitingTime, servings, public, dietaryType, ingredients, instructions)
VALUES
-- Recipes for user with profile_id 3
(3, 'Spaghetti Carbonara', 'http://localhost/recipe-roots/public/uploads/thumbnails/carbonara.jpg', 15, 20, 4, 1, NULL,
    '[{"ingredient": "Spaghetti", "amount": 400, "unit": "g"}, {"ingredient": "Eggs", "amount": 2, "unit": "pcs"}, {"ingredient": "Parmesan Cheese", "amount": 50, "unit": "g"}]',
    '1. Cook spaghetti according to package instructions. 2. In a bowl, whisk eggs and grated Parmesan. 3. Sauté diced pancetta until crispy. 4. Toss hot spaghetti with pancetta, then mix in the egg mixture. 5. Serve immediately, topped with extra Parmesan.'),
(3, 'BBQ Chicken Pizza', 'http://localhost/recipe-roots/public/uploads/thumbnails/bbq_pizza.jpg', 25, 15, 4, 1, 'halal',
    '[{"ingredient": "Pizza Base", "amount": 1, "unit": "pcs"}, {"ingredient": "BBQ Sauce", "amount": 0.5, "unit": "cup"}, {"ingredient": "Chicken Breast", "amount": 200, "unit": "g"}]',
    '1. Preheat oven to 220°C (425°F). 2. Spread BBQ sauce on the pizza base. 3. Add cooked shredded chicken and mozzarella cheese. 4. Bake for 12-15 minutes until the crust is golden. 5. Slice and serve hot.'),
(3, 'Tuna Salad', 'http://localhost/recipe-roots/public/uploads/thumbnails/tuna_salad.jpg', 10, 0, 2, 1, 'halal',
    '[{"ingredient": "Tuna", "amount": 1, "unit": "cans"}, {"ingredient": "Mayonnaise", "amount": 2, "unit": "tbsp"}, {"ingredient": "Lettuce", "amount": 1, "unit": "cups"}]',
    '1. Drain tuna and mix with mayonnaise in a bowl. 2. Chop lettuce and tomatoes, then combine with tuna mixture. 3. Serve chilled or as a sandwich filling.'),
(3, 'Vegan Pancakes', 'http://localhost/recipe-roots/public/uploads/thumbnails/vegan_pancakes.jpg', 10, 20, 4, 1, 'vegan',
    '[{"ingredient": "Flour", "amount": 1.5, "unit": "cups"}, {"ingredient": "Almond Milk", "amount": 1.25, "unit": "cups"}, {"ingredient": "Baking Powder", "amount": 1, "unit": "tsp"}]',
    '1. Mix flour, baking powder, and a pinch of salt in a bowl. 2. Gradually add almond milk, whisking until smooth. 3. Heat a non-stick pan and pour small amounts of batter. 4. Cook until bubbles appear, then flip and cook for 1-2 minutes. 5. Serve with syrup or fruit.'),
(3, 'Grilled Salmon', 'http://localhost/recipe-roots/public/uploads/thumbnails/grilled_salmon.jpg', 10, 15, 2, 1, NULL,
    '[{"ingredient": "Salmon Fillets", "amount": 2, "unit": "pcs"}, {"ingredient": "Lemon Juice", "amount": 2, "unit": "tbsp"}, {"ingredient": "Garlic", "amount": 2, "unit": "cloves"}]',
    '1. Preheat grill to medium-high heat. 2. Marinate salmon in lemon juice, minced garlic, salt, and pepper for 10 minutes. 3. Grill salmon for 6-8 minutes per side. 4. Serve with steamed vegetables or rice.'),
(3, 'Chocolate Brownies', 'http://localhost/recipe-roots/public/uploads/thumbnails/brownies.jpg', 15, 25, 9, 1, 'vegetarian',
    '[{"ingredient": "Butter", "amount": 0.5, "unit": "cups"}, {"ingredient": "Sugar", "amount": 1, "unit": "cups"}, {"ingredient": "Cocoa Powder", "amount": 0.5, "unit": "cups"}]',
    '1. Preheat oven to 175°C (350°F). 2. Melt butter and mix with sugar and cocoa powder. 3. Stir in eggs and vanilla extract until smooth. 4. Fold in flour and pour batter into a greased pan. 5. Bake for 20-25 minutes, cool, and cut into squares.'),

-- Recipes for user with profile_id 12
(12, 'Chicken Curry', 'http://localhost/recipe-roots/public/uploads/thumbnails/chicken_curry_1.jpg', 20, 40, 4, 1, 'halal',
    '[{"ingredient": "Chicken", "amount": 500, "unit": "g"}, {"ingredient": "Curry Powder", "amount": 2, "unit": "tbsp"}, {"ingredient": "Coconut Milk", "amount": 1, "unit": "cups"}]',
    '1. Heat oil in a pot and sauté chopped onions until translucent. 2. Add chicken pieces and cook until browned. 3. Stir in curry powder and cook for 2 minutes. 4. Pour in coconut milk and simmer for 30 minutes. 5. Season with salt and serve hot with rice.'),
(12, 'Fried Rice', 'http://localhost/recipe-roots/public/uploads/thumbnails/fried_rice.jpg', 15, 10, 3, 1, NULL,
    '[{"ingredient": "Cooked Rice", "amount": 2, "unit": "cups"}, {"ingredient": "Soy Sauce", "amount": 2, "unit": "tbsp"}, {"ingredient": "Mixed Vegetables", "amount": 1, "unit": "cups"}]',
    '1. Heat a wok over high heat and add 1 tbsp of oil. 2. Add diced onions and stir-fry until fragrant. 3. Stir in mixed vegetables and cook for 3 minutes. 4. Add rice and soy sauce, mixing thoroughly. 5. Serve hot, garnished with green onions.'),
    
-- Recipes for user with profile_id 18
(18, 'Lasagna', 'http://localhost/recipe-roots/public/uploads/thumbnails/lasagna.jpg', 30, 60, 6, 1, NULL,
    '[{"ingredient": "Lasagna Sheets", "amount": 12, "unit": "pcs"}, {"ingredient": "Ground Beef", "amount": 500, "unit": "g"}, {"ingredient": "Tomato Sauce", "amount": 2, "unit": "cups"}]',
    '1. Preheat oven to 180°C (350°F). 2. In a pan, sauté garlic and onions, then add ground beef and cook until browned. 3. Mix in tomato sauce and simmer for 10 minutes. 4. Layer lasagna sheets, meat sauce, and cheese in a baking dish. Repeat until all ingredients are used. 5. Bake for 45 minutes and let it cool before serving.'),
(18, 'Greek Salad', 'http://localhost/recipe-roots/public/uploads/thumbnails/greek_salad.jpeg', 10, 0, 4, 1, 'vegetarian',
    '[{"ingredient": "Cucumber", "amount": 1, "unit": "pcs"}, {"ingredient": "Tomatoes", "amount": 2, "unit": "pcs"}, {"ingredient": "Feta Cheese", "amount": 100, "unit": "g"}]',
    '1. Chop cucumber and tomatoes into bite-sized pieces and place in a salad bowl. 2. Add diced feta cheese and olives. 3. Drizzle olive oil and sprinkle oregano over the salad. 4. Toss gently and serve immediately.'),
(18, 'Lemon Cake', 'http://localhost/recipe-roots/public/uploads/thumbnails/lemon_cake.jpeg', 20, 35, 8, 1, NULL,
    '[{"ingredient": "Flour", "amount": 2, "unit": "cups"}, {"ingredient": "Sugar", "amount": 1.5, "unit": "cups"}, {"ingredient": "Lemon Juice", "amount": 0.5, "unit": "cups"}]',
    '1. Preheat oven to 175°C (350°F) and grease a cake pan. 2. In a bowl, mix flour, sugar, and baking powder. 3. Add eggs, milk, and lemon juice, whisking until smooth. 4. Pour batter into the prepared pan and bake for 30-35 minutes. 5. Let the cake cool, then drizzle with lemon glaze before serving.'),

-- Recipes for user with profile_id 25
(25, 'Vegetable Soup', 'http://localhost/recipe-roots/public/uploads/thumbnails/vegetable_soup.jpeg', 15, 25, 4, 1, 'vegan',
    '[{"ingredient": "Carrots", "amount": 2, "unit": "pcs"}, {"ingredient": "Celery", "amount": 2, "unit": "stalks"}, {"ingredient": "Vegetable Stock", "amount": 4, "unit": "cups"}]',
    '1. Heat oil in a large pot and sauté diced onions until translucent. 2. Add chopped carrots, celery, and potatoes, and cook for 5 minutes. 3. Pour in vegetable stock and bring to a boil. 4. Reduce heat and simmer for 20 minutes. 5. Blend partially for a thicker texture and serve warm.'),
(25, 'Stuffed Bell Peppers', 'http://localhost/recipe-roots/public/uploads/thumbnails/stuffed_peppers.jpg', 20, 30, 4, 1, 'vegetarian',
    '[{"ingredient": "Bell Peppers", "amount": 4, "unit": "pcs"}, {"ingredient": "Rice", "amount": 1, "unit": "cups"}, {"ingredient": "Cheese", "amount": 1, "unit": "cups"}]',
    '1. Preheat oven to 180°C (350°F). 2. Cut the tops off the bell peppers and remove seeds. 3. Cook rice until tender and mix with sautéed onions, tomatoes, and cheese. 4. Stuff the mixture into the peppers and place in a baking dish. 5. Bake for 25-30 minutes until peppers are tender. Serve immediately.');

-- Comment Table
INSERT INTO Comment (profileId, recipeId, content, rating)
VALUES
-- Comments for Classic Pancakes (Recipe ID 1)
(1, 1, 'These pancakes are so fluffy and delicious! My kids loved them.', 5),
(2, 1, 'The recipe is simple, but I think it needs a little more sugar.', 4),
(3, 1, 'The pancakes turned out great! Perfect for a Sunday breakfast.', 5),
(4, 1, 'It was good, but the texture wasnt as fluffy as I expected.', 3),
(5, 1, 'Nice recipe! Will definitely make it again, maybe add chocolate chips next time.', 4),

-- Comments for Spaghetti Bolognese (Recipe ID 2)
(6, 2, 'This spaghetti bolognese was so flavorful! Highly recommend.', 5),
(7, 2, 'The sauce was a bit runny, but overall it was good.', 3),
(8, 2, 'Simple but tasty. I added some garlic bread on the side.', 4),
(9, 2, 'It could use more seasoning, but the meat was tender.', 3),
(10, 2, 'Loved the bolognese! Perfect for a family dinner night.', 5),

-- Comments for Caesar Salad (Recipe ID 3)
(11, 3, 'Such a refreshing salad! The Caesar dressing made it perfect.', 5),
(12, 3, 'I think it needed more croutons for some crunch.', 4),
(13, 3, 'Great salad, but I prefer adding grilled chicken to it.', 4),
(14, 3, 'The dressing was a bit too creamy for my taste.', 3),
(15, 3, 'Fresh and light! I used homemade dressing and it was amazing.', 5),

-- Comments for Vegetable Stir Fry (Recipe ID 4)
(16, 4, 'Quick and easy to make, and the veggies tasted amazing.', 5),
(17, 4, 'Good recipe, but I added some chili flakes for extra spice.', 4),
(18, 4, 'The stir fry was okay, but I wish the vegetables were a bit more crispy.', 3),
(19, 4, 'A great way to use up leftover vegetables, will make it again.', 4),
(20, 4, 'Perfect! So healthy and the flavours were great.', 5),

-- Comments for Chocolate Chip Cookies (Recipe ID 5)
(21, 5, 'Best cookies ever! Crispy on the outside and gooey inside.', 5),
(22, 5, 'I think the butter amount was too high, they spread too much.', 3),
(23, 5, 'These cookies are a hit in my family! Will make them again for sure.', 5),
(24, 5, 'The dough was a bit too sticky, but they turned out fine in the end.', 4),
(25, 5, 'Love the taste, but I would reduce the sugar next time for less sweetness.', 4),

-- Comments for Grilled Chicken (Recipe ID 6)
(26, 6, 'Grilled to perfection! The spices were amazing.', 5),
(27, 6, 'I marinated it overnight and it turned out super tender and flavourful.', 5),
(28, 6, 'It was good, but I think the chicken could have been a little juicier.', 3),
(29, 6, 'Simple yet delicious. I served it with some roasted veggies.', 4),
(30, 6, 'Great recipe! I love how quick it is to make.', 5),

-- Comments for Tomato Soup (Recipe ID 7)
(21, 7, 'This soup is so comforting, perfect for a cold evening.', 5),
(22, 7, 'It needed a little more seasoning, but it was still tasty.', 4),
(23, 7, 'I love the simplicity of this soup. Will make it again next winter.', 5),
(24, 7, 'Good base, but I added some cream to make it richer.', 4),
(25, 7, 'A very basic soup, not too exciting for me, but it’s okay.', 3),

-- Comments for Beef Tacos (Recipe ID 8)
(30, 8, 'Delicious tacos! The beef was so flavorful.', 5),
(17, 8, 'Loved the recipe! I added some salsa for extra flavor.', 4),
(28, 8, 'These tacos were easy to make, but I feel like they need more seasoning.', 3),
(3, 8, 'Perfect for taco night! Everyone loved them.', 5),
(4, 8, 'The taco shells were a bit too hard for my liking.', 2),

-- Comments for Veggie Pizza (Recipe ID 9)
(4, 9, 'Such a great pizza! The veggies were fresh and tasty.', 5),
(12, 9, 'I prefer more cheese on my pizza, but overall it was good.', 4),
(23, 9, 'The crust did not turn out as crispy as I wanted.', 3),
(14, 9, 'Perfect for vegetarians! I added mushrooms and spinach as extra toppings.', 5),
(15, 9, 'Love the idea of veggie pizza, but I feel like it needs more flavour.', 3),

-- Comments for Fruit Smoothie (Recipe ID 10)
(6, 10, 'A refreshing smoothie! So easy to make and delicious.', 5),
(37, 10, 'The smoothie was good, but I added honey for some sweetness.', 4),
(18, 10, 'Perfect for a quick breakfast, but I would prefer more fruit in it.', 4),
(9, 10, 'I loved the combination of banana and berries!', 5),
(5, 10, 'It was fine, but I think it could use a bit more ice to make it colder.', 3),

-- Comments for Avocado Toast (Recipe ID 11)
(1, 11, 'Such a simple and healthy breakfast! Love how quick it is to make.', 5),
(2, 11, 'It is good, but I think it needs more toppings like a poached egg or tomatoes.', 4),
(3, 11, 'A perfect meal for mornings! I add some chili flakes for a bit of heat.', 5),
(4, 11, 'It was okay, but the bread could have been a little crispier for my taste.', 3),
(5, 11, 'Love the combination of avocado and toast! My new favourite breakfast.', 5),

-- Comments for Chicken Curry (Recipe ID 12)
(6, 12, 'The curry turned out so flavourful! Definitely a keeper for weeknight dinners.', 5),
(7, 12, 'Loved it! The coconut milk made it so creamy, but it needed a little more spice.', 4),
(8, 12, 'It was good, but I think it could use some extra vegetables in the mix.', 3),
(9, 12, 'I made it with rice and it was perfect. The chicken was tender and flavourful!', 5),
(10, 12, 'Quick and easy, but I would prefer a little more curry powder for extra flavour.', 4),

-- Comments for Garlic Bread (Recipe ID 13)
(11, 13, 'So crispy and buttery! Perfect with pasta or as a side dish.', 5),
(12, 13, 'The garlic flavour was strong, just how I like it! Perfectly crispy.', 5),
(13, 13, 'It was good, but I think it needed a little more butter for a richer taste.', 4),
(14, 13, 'Simple and tasty, but I would have liked a bit more garlic in mine.', 4),
(15, 13, 'Great recipe! I served this with a salad, and it was amazing.', 5),

-- Comments for Berry Parfait (Recipe ID 14)
(16, 14, 'A refreshing treat! Great for breakfast or a light dessert.', 5),
(17, 14, 'I love this! I used fresh blueberries and added a bit of honey on top.', 5),
(18, 14, 'Delicious and easy to prepare, but I think it could use a bit more granola.', 4),
(19, 14, 'It was good, but I added some chia seeds for an extra crunch.', 4),
(20, 14, 'My kids loved this! It is a perfect healthy snack for them.', 5),

-- Comments for Scrambled Eggs (Recipe ID 15)
(21, 15, 'So easy and tasty! My go-to breakfast every morning.', 5),
(22, 15, 'The eggs turned out great! I added some cheese and it was delicious.', 5),
(23, 15, 'It was okay, but I think the eggs could have been a little fluffier.', 3),
(24, 15, 'I love how simple this is, and the butter makes it taste amazing!', 4),
(25, 15, 'Quick and easy breakfast, but I would have preferred them to be less runny.', 4),

-- Comments for Banana Bread (Recipe ID 16)
(26, 16, 'This banana bread is so moist and delicious! Perfect with a cup of tea.', 5),
(27, 16, 'It was very good, but I think it needed a bit more sugar to balance the bananas.', 4),
(28, 16, 'The texture was great, but it did not rise as much as I hoped.', 3),
(29, 16, 'Made it twice! My family loves it. I added some walnuts for crunch.', 5),
(30, 16, 'Delicious and easy to make! The bananas made it so sweet and moist.', 5),

-- Comments for Caprese Salad (Recipe ID 17)
(1, 17, 'A perfect summer salad! Fresh and light.', 5),
(2, 17, 'Love this salad! I added a drizzle of balsamic vinegar for extra flavour.', 5),
(3, 17, 'It was good, but I think it needs more mozzarella for a creamier texture.', 4),
(4, 17, 'A simple and tasty salad, but I added some olives for more flavour.', 4),
(9, 17, 'This is my go-to salad when I want something quick and fresh.', 5),

-- Comments for Grilled Salmon (Recipe ID 18)
(36, 18, 'The salmon was cooked perfectly, and the lemon added great flavour.', 5),
(7, 18, 'I loved the recipe! Simple and healthy, but I added some garlic for more depth of flavour.', 4),
(8, 18, 'It was good, but the salmon could have been a bit juicier.', 3),
(9, 18, 'Perfect for a light dinner! I served it with a side of steamed vegetables.', 5),
(10, 18, 'The salmon was delicious, but I feel like it could have used a bit more seasoning.', 4),

-- Comments for Minestrone Soup (Recipe ID 19)
(11, 19, 'A hearty and filling soup! I love how easy it is to make.', 5),
(12, 19, 'Great flavour, but I think it needed a little more salt.', 4),
(13, 19, 'A comforting soup! Perfect for a cold evening, but I added some beans for extra protein.', 5),
(14, 19, 'It was good, but I prefer a thicker consistency in my minestrone.', 3),
(15, 19, 'Delicious soup! I added some spinach and it was even better.', 4),

-- Comments for Stuffed Bell Peppers (Recipe ID 20)
(26, 20, 'These stuffed peppers were a hit at dinner! So flavourful and filling.', 5),
(27, 20, 'Loved this recipe! The rice and beef filling was delicious.', 5),
(28, 20, 'It was good, but I think it could use more seasoning in the stuffing.', 4),
(29, 20, 'The peppers were cooked perfectly, but I feel like the stuffing could use more veggies.', 4),
(20, 20, 'This recipe is perfect for meal prep! I made a batch and froze the leftovers.', 5);

--Ingredient Table
INSERT INTO Ingredient (farmerId, ingredient, price, unit, thumbnail, unlisted)
VALUES
-- Farmer 31 ingredients
(31, 'Tomatoes', 11.25, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/tomatoes.jpg', 0),
(31, 'Cucumbers', 8.10, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/cucumbers.jpg', 0),
(31, 'Eggs', 5.40, 'dozen', 'http://localhost/recipe-roots/public/uploads/thumbnails/eggs.jpg', 0),
(31, 'Fresh Milk', 6.75, 'L', 'http://localhost/recipe-roots/public/uploads/thumbnails/fresh_milk.png', 0),

-- Farmer 32 ingredients
(32, 'Carrots', 9.90, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/carrots.jpg', 0),
(32, 'Potatoes', 6.75, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/potatoes.jpg', 0),
(32, 'Orange Juice', 15.75, 'L', 'http://localhost/recipe-roots/public/uploads/thumbnails/orange_juice.jpg', 0),
(32, 'Beets', 13.50, 'quart', 'http://localhost/recipe-roots/public/uploads/thumbnails/beets.jpeg', 0),

-- Farmer 33 ingredients
(33, 'Onions', 7.65, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/onions.jpg', 0),
(33, 'Garlic', 15.30, 'g', 'http://localhost/recipe-roots/public/uploads/thumbnails/garlic.jpg', 0),
(33, 'Shallots', 13.05, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/shallots.jpg',0),
(33, 'Yogurt', 11.25, 'mL', 'http://localhost/recipe-roots/public/uploads/thumbnails/yogurt.jpg', 0),

-- Farmer 34 ingredients
(34, 'Bell Peppers', 13.50, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/bell_peppers.jpg', 0),
(34, 'Spinach', 20.25, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/spinach.jpg', 0),
(34, 'Kale', 18.00, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/kale.jpg', 0),
(34, 'Coconut Water', 18.90, 'L', 'http://localhost/recipe-roots/public/uploads/thumbnails/coconut_water.jpg', 0),

-- Farmer 35 ingredients
(35, 'Strawberries', 27.90, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/strawberries.jpg', 0),
(35, 'Blueberries', 37.35, 'g', 'http://localhost/recipe-roots/public/uploads/thumbnails/blueberries.jpg', 0),
(35, 'Raspberries', 40.50, 'g', 'http://localhost/recipe-roots/public/uploads/thumbnails/raspberries.jpg', 0),
(35, 'Honey', 56.25, 'gallon', 'http://localhost/recipe-roots/public/uploads/thumbnails/honey.jpg', 0),

-- Farmer 36 ingredients
(36, 'Eggplant', 13.05, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/eggplant.jpg', 0),
(36, 'Zucchini', 11.70, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/zucchini.jpg', 0),
(36, 'Pumpkin Juice', 13.95, 'ml', 'http://localhost/recipe-roots/public/uploads/thumbnails/pumpkin_juice.jpg', 0),
(36, 'Soy Milk', 11.25, 'L', 'http://localhost/recipe-roots/public/uploads/thumbnails/soy_milk.jpg', 0),

-- Farmer 37 ingredients
(37, 'Mangoes', 24.75, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/mangoes.jpg', 0),
(37, 'Pineapples', 21.15, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/pineapples.jpg', 0),
(37, 'Papayas', 22.05, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/papayas.jpg', 0),
(37, 'Almond Milk', 17.55, 'L', 'http://localhost/recipe-roots/public/uploads/thumbnails/almond_milk.jpg', 0),

-- Farmer 38 ingredients
(38, 'Bananas', 9.45, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/bananas.jpg', 0),
(38, 'Apples', 17.10, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/apples.jpg', 0),
(38, 'Pears', 15.75, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/pears.jpg', 0),
(38, 'Apple Cider', 27.00, 'gallon', 'http://localhost/recipe-roots/public/uploads/thumbnails/apple_cider.jpeg', 0),

-- Farmer 39 ingredients
(39, 'Broccoli', 18.45, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/broccoli.jpg', 0),
(39, 'Cauliflower', 16.65, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/cauliflower.jpg', 0),
(39, 'Cabbage', 10.80, 'dozen', 'http://localhost/recipe-roots/public/uploads/thumbnails/cabbage.jpg', 0),
(39, 'Goat Milk', 18.00, 'L', 'http://localhost/recipe-roots/public/uploads/thumbnails/goat_milk.jpeg', 0),

-- Farmer 40 ingredients
(40, 'Green Beans', 14.85, 'kg', 'http://localhost/recipe-roots/public/uploads/thumbnails/green_beans.jpeg', 0),
(40, 'Peas', 12.60, 'g', 'http://localhost/recipe-roots/public/uploads/thumbnails/peas.jpg', 0),
(40, 'Asparagus', 24.75, 'quart', 'http://localhost/recipe-roots/public/uploads/thumbnails/asparagus.jpeg', 0),
(40, 'Rice Milk', 15.75, 'L', 'http://localhost/recipe-roots/public/uploads/thumbnails/rice_milk.jpg', 0);

--Purchase Table
INSERT INTO Purchase (farmerId, ingredientId, amount, createdAt)
VALUES
-- Purchases by various users from different farmers
(31, 5, 10, '2024-01-15 10:00:00'), -- User 1 buys 10 units of ingredient 5 from farmer 31
(32, 3, 15, '2024-01-01 10:00:00'), -- User 2 buys 15 units of ingredient 3 from farmer 32
(33, 7, 8, '2024-01-14 10:00:00'),  -- User 3 buys 8 units of ingredient 7 from farmer 33
(34, 2, 12, '2024-01-13 10:00:00'), -- User 4 buys 12 units of ingredient 2 from farmer 34
(31, 1, 5, '2024-03-25 10:00:00'),  -- User 5 buys 5 units of ingredient 1 from farmer 31

-- User with multiple purchases
(32, 3, 20, '2024-03-15 10:00:00'), -- User 6 buys 20 units of ingredient 3 from farmer 32
(33, 4, 15, '2024-04-11 10:00:00'), -- User 6 buys 15 units of ingredient 4 from farmer 33
(34, 5, 10, '2024-10-10 10:00:00'), -- User 6 buys 10 units of ingredient 5 from farmer 34

-- Another set of purchases
(35, 6, 25, '2024-08-03 10:00:00'), -- User 7 buys 25 units of ingredient 6 from farmer 35
(36, 8, 30,'2024-09-20 10:00:00'), -- User 8 buys 30 units of ingredient 8 from farmer 36
(37, 9, 18, '2024-03-23 10:00:00'), -- User 9 buys 18 units of ingredient 9 from farmer 37
(38, 10, 22, '2024-09-15 10:00:00'), -- User 10 buys 22 units of ingredient 10 from farmer 38
(31, 11, 14, '2024-05-20 10:00:00'), -- User 11 buys 14 units of ingredient 11 from farmer 31

-- Bulk purchase example
(32, 12, 50, '2024-12-09 10:00:00'), -- User 12 buys 50 units of ingredient 12 from farmer 32
(33, 13, 40, '2024-09-15 10:00:00'), -- User 13 buys 40 units of ingredient 13 from farmer 33

-- More data values

-- User 20 purchases
(31, 2, 6, '2024-08-20 10:00:00'),
(32, 3, 4, '2024-01-15 10:00:00'),

-- User 21 purchases
(33, 5, 9, '2024-05-08 10:00:00'),
(34, 8, 3, '2024-06-10 10:00:00'),

-- User 22 purchases
(35, 1, 7, '2024-03-11 10:00:00'),
(36, 6, 5, '2024-04-02 10:00:00'),

-- User 23 purchases
(37, 9, 2, '2024-04-15 10:00:00'),
(38, 10, 8, '2024-10-07 10:00:00'),

-- User 24 purchases
(39, 4, 6, '2024-02-10 10:00:00'),
(40, 7, 3, '2024-03-20 10:00:00'),

-- User 25 purchases
(31, 3, 10, '2024-10-20 10:00:00'),
(33, 1, 4, '2024-12-15 10:00:00'),

-- User 26 purchases
(32, 6, 5, '2024-12-10 10:00:00'),
(34, 2, 7, '2024-09-09 10:00:00'),

-- User 27 purchases
(35, 4, 3, '2024-11-11 10:00:00'),
(36, 5, 8, '2024-09-05 10:00:00'),

-- User 28 purchases
(37, 8, 2, '2024-04-20 10:00:00'),
(39, 9, 6, '2024-06-06 10:00:00'),

-- User 29 purchases
(38, 7, 9,'2024-07-01 10:00:00'),
(40, 10, 4, '2024-11-15 10:00:00');

--Invoice Table
-- Invoice for User 1 (purchases 1, 2, 3, 4, 5)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_1_1734081951', 1, '[1, 2, 3, 4, 5]');

-- Invoice for User 2 (purchases 6, 7, 8, 9, 10)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_2_1734081991', 2, '[6, 7, 8, 9, 10]');

-- Invoice for User 3 (purchases 11, 12, 13)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_3_1734082022', 3, '[11, 12, 13]');

-- Invoice for User 4 (purchases 14, 15)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_4_1734082035', 4, '[14, 15]');

-- Invoice for User 5 (purchases 16, 17)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_5_1734082051', 5, '[16, 17]');

-- Invoice for User 6 (purchases 18, 19, 20)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_6_1734082099', 6, '[18, 19, 20]');

-- Invoice for User 7 (purchase 21)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_7_1734082110', 7, '[21]');

-- Invoice for User 8 (purchase 22)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_8_1734082126', 8, '[22]');

-- Invoice for User 9 (purchase 23)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_9_1734082235', 9, '[23]');

-- Invoice for User 10 (purchase 24)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_10_1734082221', 10, '[24]');

-- Invoice for User 12 (purchases 25, 26, 27)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_12_1734082198', 12, '[25, 26, 27]');

-- Invoice for User 13 (purchases 28, 29)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_13_1734082248', 13, '[28, 29]');

-- Invoice for User 20 (purchases 30, 31)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_20_1734082264', 20, '[30, 31]');

-- Invoice for User 21 (purchases 32, 33)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_21_1734082285', 21, '[32, 33]');

-- Invoice for User 22 (purchases 34, 35)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_22_1734082299', 22, '[34, 35]');

-- Invoice for User 23 (purchases 36, 37)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_23_1734082315', 23, '[36, 37]');

-- Invoice for User 24 (purchases 38, 39)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_24_1734082331', 24, '[38, 39]');

-- Invoice for User 25 (purchases 40, 41)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('irr_25_1734082348', 25, '[40, 41]');

-- Invoice for User 26 (purchases 42, 43)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_26_1734082359', 26, '[42, 43]');

-- Invoice for User 27 (purchases 44, 45)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_27_1734082375', 27, '[44, 45]');

-- Invoice for User 28 (purchases 46, 47)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_28_1734082388', 28, '[46, 47]');

-- Invoice for User 29 (purchases 48, 49)
INSERT INTO Invoice (invoiceId, profileId, purchaseIds)
VALUES
('rr_29_1734082402', 29, '[48, 49]');

-- Cookbook Table
INSERT INTO Cookbook (profileId, title, description, public, thumbnail) 
VALUES
-- Recipes for user with profile_id 30
(3, 'Cooking for Dummies','Dive into the world of culinary creativity with this delightful cookbook, featuring a diverse collection of six irresistible recipes that cater to every palate. Whether you are a seasoned chef or a kitchen newbie, this book offers a variety of dishes, each crafted to bring flavors to life with simplicity and ease.

Quick and Classic: Whip up the creamy indulgence of Spaghetti Carbonara or savor the smoky goodness of a BBQ Chicken Pizza. Both are perfect for satisfying hearty appetites with minimal effort.
Healthy and Fresh: Enjoy the zesty freshness of a Tuna Salad or the wholesome simplicity of Grilled Salmon, ideal for light yet fulfilling meals.
Special Diet Options: Delight in the fluffy perfection of Vegan Pancakes, a plant-based breakfast favorite, or indulge your sweet tooth with decadent Chocolate Brownies that are vegetarian-friendly.
Complete with vibrant thumbnails for every dish, this cookbook is your ultimate guide to mastering quick prep times, achieving restaurant-quality results, and delighting family and friends with meals that suit any occasion or dietary preference. Get ready to explore new flavors, one recipe at a time!' ,1, 'http://localhost/recipe-roots/public/uploads/thumbnails/grilled_salmon.jpg');

-- CookbookJoin Table
INSERT INTO CookbookJoin (cookbookId, recipeId) 
VALUES
(1, 3),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26);

