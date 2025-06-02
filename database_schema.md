# Database Schema Documentation

## users
| Column              | Type              | Notes                |
|---------------------|-------------------|----------------------|
| id                  | bigIncrements     | Primary key          |
| name                | string            |                      |
| email               | string            | Unique               |
| email_verified_at   | timestamp         | Nullable             |
| password            | string            |                      |
| remember_token      | string            | Nullable             |
| created_at/updated_at | timestamps      |                      |

---

## password_reset_tokens
| Column      | Type    | Notes      |
|-------------|---------|------------|
| email       | string  | Primary key|
| token       | string  |            |
| created_at  | timestamp | Nullable |

---

## sessions
| Column      | Type    | Notes      |
|-------------|---------|------------|
| id          | string  | Primary key|
| ...         | ...     | Session data|

---

## cache
| Column      | Type    | Notes      |
|-------------|---------|------------|
| key         | string  | Primary key|
| value       | mediumText |         |
| expiration  | integer |           |

---

## cache_locks
| Column      | Type    | Notes      |
|-------------|---------|------------|
| key         | string  | Primary key|
| owner       | string  |            |
| expiration  | integer |            |

---

## jobs
| Column      | Type    | Notes      |
|-------------|---------|------------|
| id          | bigIncrements | Primary key|
| queue       | string  | Indexed   |
| payload     | longText|           |
| attempts    | unsignedTinyInteger | |
| reserved_at | unsignedInteger | Nullable |
| available_at| unsignedInteger |         |
| created_at  | unsignedInteger |         |

---

## job_batches
| Column      | Type    | Notes      |
|-------------|---------|------------|
| id          | string  | Primary key|
| name        | string  |            |
| total_jobs  | integer |            |
| pending_jobs| integer |            |
| failed_jobs | integer |            |
| failed_job_ids | longText |        |
| options     | mediumText | Nullable|

---

## settings
| Column        | Type      | Notes      |
|---------------|-----------|------------|
| id            | bigIncrements | Primary key|
| key_name      | string(191)   |           |
| value         | json      | Nullable   |
| settings_type | string(191) | Nullable |
| created_at/updated_at | timestamps |   |

---

## units
| Column      | Type    | Notes      |
|-------------|---------|------------|
| id          | bigIncrements | Primary key|
| name        | string  | Unique     |
| symbol      | string  | Unique, Indexed|
| unit_type   | string  | Nullable, Indexed|
| is_active   | boolean | Default true, Indexed|
| deleted_at  | softDeletes |         |
| created_at/updated_at | timestamps |   |

---

## categories
| Column      | Type    | Notes      |
|-------------|---------|------------|
| id          | bigIncrements | Primary key|
| name        | string  | Indexed   |
| slug        | string  | Unique    |
| description | text    | Nullable  |
| image_path  | string  | Nullable  |
| is_active   | boolean | Default true, Indexed|
| position    | integer | Default 0, Indexed|
| created_at/updated_at | timestamps |   |
| deleted_at  | softDeletes |         |

---

## markets
| Column      | Type    | Notes      |
|-------------|---------|------------|
| id          | bigIncrements | Primary key|
| name        | string  |            |
| slug        | string  | Unique     |
| description | text    | Nullable   |
| image_path  | string  | Nullable   |
| location    | string  | Nullable   |
| type        | string  | Nullable   |
| address     | string  | Nullable   |
| latitude    | decimal(10,8) | Nullable |
| longitude   | decimal(11,8) | Nullable |
| phone       | string  | Nullable   |
| email       | string  | Nullable   |
| website     | string  | Nullable   |
| opening_hours | json  | Nullable   |
| rating      | decimal(3,2) | Nullable |
| rating_count| integer | Default 0  |
| is_active   | boolean | Default true|
| created_at/updated_at | timestamps |   |
| deleted_at  | softDeletes |         |

---

## products
| Column      | Type    | Notes      |
|-------------|---------|------------|
| id          | bigIncrements | Primary key|
| name        | string  |            |
| category_id | foreignId | Constrained, Cascade delete|
| unit_id     | foreignId | Constrained, Cascade delete|
| description | text    | Nullable   |
| status      | enum('active','inactive','draft') | Default 'active'|
| is_visible  | boolean | Default true|
| is_featured | boolean | Default false|
| image_path  | string  | Nullable   |
| sku         | string  | Unique, Nullable|
| barcode     | string  | Unique, Nullable|
| brand       | string  | Nullable   |
| base_price  | decimal(10,2) | Nullable|
| stock       | integer | Default 0  |
| created_at/updated_at | timestamps |   |
| deleted_at  | softDeletes |         |

---

## product_tags
| Column      | Type    | Notes      |
|-------------|---------|------------|
| id          | bigIncrements | Primary key|
| product_id  | foreignId |            |
| tag         | string  |            |
| created_at/updated_at | timestamps |   |
| unique(product_id, tag) |           |
| index(tag)  |         |

---

## product_market_prices
| Column      | Type    | Notes      |
|-------------|---------|------------|
| id          | bigIncrements | Primary key|
| product_id  | foreignId |            |
| market_id   | foreignId |            |
| price       | decimal(10,2) |        |
| discount_price | decimal(10,2) | Nullable|
| price_date  | timestamp | Default current|
| created_at/updated_at | timestamps |   |
| unique(product_id, market_id, price_date) | |
| index(price) |         |
| index(price_date) |   |
| index(product_id, market_id) | Composite index|

---

## price_contributions
| Column          | Type            | Notes      |
|-----------------|-----------------|------------|
| id              | bigIncrements   | Primary key|
| product_id      | foreignId       | Constrained|
| market_id       | foreignId       | Constrained|
| user_id         | foreignId       | Constrained|
| submitted_price | decimal(10,2)   |            |
| proof_image     | string          | Nullable   |
| status          | enum('pending','approved','rejected') | Default 'pending'|
| upvotes         | integer         | Default 0  |
| downvotes       | integer         | Default 0  |
| verified_at     | timestamp       | Nullable   |
| created_at/updated_at | timestamps |           |
| index(product_id, market_id, status) |         |
| index(user_id)  |                 |
| index(created_at) |               |

---

## price_contribution_votes
| Column                | Type            | Notes                        |
|-----------------------|-----------------|------------------------------|
| id                    | bigIncrements   | Primary key                  |
| price_contribution_id | foreignId       | Constrained, Cascade delete  |
| user_id               | foreignId       | Constrained                  |
| is_upvote             | boolean         |                              |
| created_at/updated_at | timestamps      |                              |
| unique(price_contribution_id, user_id)  |                              |

---

## price_thresholds
| Column      | Type            | Notes                        |
|-------------|-----------------|------------------------------|
| id          | bigIncrements   | Primary key                  |
| product_id  | foreignId       | Constrained                  |
| min_price   | decimal(10,2)   |                              |
| max_price   | decimal(10,2)   |                              |
| created_at/updated_at | timestamps |                          |
| index(product_id)     |            |                            |

---

# Notes
- All tables have standard Laravel timestamps unless otherwise noted.
- Foreign keys are constrained and cascade on delete where specified.
- Indexes and unique constraints are included for performance and data integrity.
