# Table: price_contributions
| Column            | Type                        | Notes                        |
|-------------------|-----------------------------|------------------------------|
| id                | bigIncrements               | Primary key                  |
| product_id        | foreignId                   | Constrained                  |
| market_id         | foreignId                   | Constrained                  |
| user_id           | foreignId                   | Constrained                  |
| submitted_price   | decimal(10,2)               |                              |
| proof_image       | string                      | Nullable                     |
| status            | enum('pending','approved','rejected') | Default 'pending' |
| upvotes           | integer                     | Default 0                    |
| downvotes         | integer                     | Default 0                    |
| verified_at       | timestamp                   | Nullable                     |
| created_at/updated_at | timestamps               |                              |
| index(product_id, market_id, status) |           |                              |
| index(user_id)    |                             |                              |
| index(created_at) |                             |                              |

---

# Table: price_contribution_votes
| Column                | Type            | Notes                        |
|-----------------------|-----------------|------------------------------|
| id                    | bigIncrements   | Primary key                  |
| price_contribution_id | foreignId       | Constrained, Cascade delete  |
| user_id               | foreignId       | Constrained                  |
| is_upvote             | boolean         |                              |
| created_at/updated_at | timestamps      |                              |
| unique(price_contribution_id, user_id)  |                              |

---

# Table: price_thresholds
| Column      | Type            | Notes                        |
|-------------|-----------------|------------------------------|
| id          | bigIncrements   | Primary key                  |
| product_id  | foreignId       | Constrained                  |
| min_price   | decimal(10,2)   |                              |
| max_price   | decimal(10,2)   |                              |
| created_at/updated_at | timestamps |                          |
| index(product_id)     |            |                            |
