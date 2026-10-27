-- ==============================================================================
-- MERCURI CRM - LIVE DATABASE CLEANUP & DEDUPLICATION SCRIPT
-- Purpose: Safely merge duplicate Product Groups (SubCategories) and add UNIQUE constraint.
-- Safe to run directly in phpMyAdmin / MySQL CLI on Live database.
-- ==============================================================================

START TRANSACTION;

-- ------------------------------------------------------------------------------
-- 1. REACH TRUCK (cat_id = 4)
-- Remap all products pointing to duplicate Reach Truck IDs to Master ID 21
-- ------------------------------------------------------------------------------
UPDATE products 
SET sub_cat_id = 21 
WHERE sub_cat_id IN (
    SELECT id FROM (
        SELECT id FROM product_sub_categories 
        WHERE cat_id = 4 
          AND LOWER(REPLACE(subcategory_name, ' ', '')) = 'reachtruck' 
          AND id != 21
    ) AS tmp_reach
);

-- Remap in terms table (if any reference exists)
UPDATE terms 
SET product_sub_category_id = 21 
WHERE product_sub_category_id IN (
    SELECT id FROM (
        SELECT id FROM product_sub_categories 
        WHERE cat_id = 4 
          AND LOWER(REPLACE(subcategory_name, ' ', '')) = 'reachtruck' 
          AND id != 21
    ) AS tmp_reach_terms
);

-- Remap in leads table (if any reference exists)
UPDATE leads 
SET subcategory_id = 21 
WHERE subcategory_id IN (
    SELECT id FROM (
        SELECT id FROM product_sub_categories 
        WHERE cat_id = 4 
          AND LOWER(REPLACE(subcategory_name, ' ', '')) = 'reachtruck' 
          AND id != 21
    ) AS tmp_reach_leads
);

-- Delete redundant Reach Truck duplicate rows
DELETE FROM product_sub_categories 
WHERE cat_id = 4 
  AND LOWER(REPLACE(subcategory_name, ' ', '')) = 'reachtruck' 
  AND id != 21;

-- Ensure standardized name for Master ID 21
UPDATE product_sub_categories 
SET subcategory_name = 'Reach Truck' 
WHERE id = 21;


-- ------------------------------------------------------------------------------
-- 2. ELECTRIC FORKLIFT (cat_id = 4)
-- Remap all products pointing to duplicate Electric Forklift IDs to Master ID 12
-- ------------------------------------------------------------------------------
UPDATE products 
SET sub_cat_id = 12 
WHERE sub_cat_id IN (
    SELECT id FROM (
        SELECT id FROM product_sub_categories 
        WHERE cat_id = 4 
          AND LOWER(REPLACE(subcategory_name, ' ', '')) = 'electricforklift' 
          AND id != 12
    ) AS tmp_fork
);

UPDATE terms 
SET product_sub_category_id = 12 
WHERE product_sub_category_id IN (
    SELECT id FROM (
        SELECT id FROM product_sub_categories 
        WHERE cat_id = 4 
          AND LOWER(REPLACE(subcategory_name, ' ', '')) = 'electricforklift' 
          AND id != 12
    ) AS tmp_fork_terms
);

UPDATE leads 
SET subcategory_id = 12 
WHERE subcategory_id IN (
    SELECT id FROM (
        SELECT id FROM product_sub_categories 
        WHERE cat_id = 4 
          AND LOWER(REPLACE(subcategory_name, ' ', '')) = 'electricforklift' 
          AND id != 12
    ) AS tmp_fork_leads
);

-- Delete redundant Electric Forklift duplicate rows
DELETE FROM product_sub_categories 
WHERE cat_id = 4 
  AND LOWER(REPLACE(subcategory_name, ' ', '')) = 'electricforklift' 
  AND id != 12;

-- Ensure standardized name for Master ID 12
UPDATE product_sub_categories 
SET subcategory_name = 'Electric Forklift' 
WHERE id = 12;


-- ------------------------------------------------------------------------------
-- 3. WALKIE ELECTRIC PALLET TRUCK (cat_id = 4)
-- Remap all products pointing to duplicate Walkie IDs to Master ID 7
-- ------------------------------------------------------------------------------
UPDATE products 
SET sub_cat_id = 7 
WHERE sub_cat_id IN (
    SELECT id FROM (
        SELECT id FROM product_sub_categories 
        WHERE cat_id = 4 
          AND LOWER(REPLACE(subcategory_name, ' ', '')) = 'walkieelectricpallettruck' 
          AND id != 7
    ) AS tmp_walkie
);

UPDATE terms 
SET product_sub_category_id = 7 
WHERE product_sub_category_id IN (
    SELECT id FROM (
        SELECT id FROM product_sub_categories 
        WHERE cat_id = 4 
          AND LOWER(REPLACE(subcategory_name, ' ', '')) = 'walkieelectricpallettruck' 
          AND id != 7
    ) AS tmp_walkie_terms
);

UPDATE leads 
SET subcategory_id = 7 
WHERE subcategory_id IN (
    SELECT id FROM (
        SELECT id FROM product_sub_categories 
        WHERE cat_id = 4 
          AND LOWER(REPLACE(subcategory_name, ' ', '')) = 'walkieelectricpallettruck' 
          AND id != 7
    ) AS tmp_walkie_leads
);

-- Delete redundant Walkie duplicate rows
DELETE FROM product_sub_categories 
WHERE cat_id = 4 
  AND LOWER(REPLACE(subcategory_name, ' ', '')) = 'walkieelectricpallettruck' 
  AND id != 7;

-- Ensure standardized name for Master ID 7
UPDATE product_sub_categories 
SET subcategory_name = 'Walkie Electric Pallet Truck' 
WHERE id = 7;


-- ------------------------------------------------------------------------------
-- 4. ADD UNIQUE CONSTRAINT
-- Prevents MySQL from ever inserting duplicates in product_sub_categories again
-- ------------------------------------------------------------------------------
ALTER TABLE `product_sub_categories` 
ADD UNIQUE KEY `unique_cat_subcat` (`cat_id`, `subcategory_name`);

COMMIT;
