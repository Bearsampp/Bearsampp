/* Copyright (c) 2015, 2025, Oracle and/or its affiliates.

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License, version 2.0,
   as published by the Free Software Foundation.

   This program is designed to work with certain software (including
   but not limited to OpenSSL) that is licensed under separate terms,
   as designated in a particular file or component or in included license
   documentation.  The authors of MySQL hereby grant you an additional
   permission to link the program and your derivative works with the
   separately licensed software that they have either included with
   the program or referenced in the documentation.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License, version 2.0, for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301  USA */

CREATE DATABASE IF NOT EXISTS query_rewrite;

CREATE TABLE IF NOT EXISTS query_rewrite.rewrite_rules (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  pattern VARCHAR(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  pattern_database VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  replacement VARCHAR(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  enabled ENUM('YES', 'NO') CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
    DEFAULT 'YES',
  message VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  pattern_digest VARCHAR(64),
  normalized_pattern VARCHAR(100)
) DEFAULT CHARSET = utf8mb4 ENGINE = INNODB;

INSTALL PLUGIN rewriter SONAME 'rewriter.dll';
CREATE FUNCTION load_rewrite_rules RETURNS STRING
SONAME 'rewriter.dll';

DELIMITER //

CREATE PROCEDURE query_rewrite.flush_rewrite_rules()
BEGIN
  DECLARE message_text VARCHAR(100);
  COMMIT;
  SELECT load_rewrite_rules() INTO message_text;
  IF NOT message_text IS NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = message_text;
  END IF;
END //

DELIMITER ;
