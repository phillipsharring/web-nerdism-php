-- The phone column was a mistake. Undoing it is a new change, like any other.
ALTER TABLE `users`
DROP COLUMN `phone`;
