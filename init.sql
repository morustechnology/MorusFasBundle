INSERT INTO `fas_config`(`controlCode`, `value`, `name`, `description`, `sort_order`, `active`, `create_date`) VALUES ('STATEMENT_CONFIG', '', 'Statement Header Configuration', 'Keep Statment Header Pairing Info');

INSERT INTO `fas_statement_status`(`control_code`, `name`, `sort_order`, `active`, `create_date`) VALUES ('NEW', 'New', 0, 1, NOW());
INSERT INTO `fas_statement_status`(`control_code`, `name`, `sort_order`, `active`, `create_date`) VALUES ('PROCESS', 'Processing', 1, 1, NOW());
INSERT INTO `fas_statement_status`(`control_code`, `name`, `sort_order`, `active`, `create_date`) VALUES ('ERROR', 'Contain Error', 2, 1, NOW());
INSERT INTO `fas_statement_status`(`control_code`, `name`, `sort_order`, `active`, `create_date`) VALUES ('FAIL', 'Import Fail', 3, 1, NOW());
INSERT INTO `fas_statement_status`(`control_code`, `name`, `sort_order`, `active`, `create_date`) VALUES ('COMPLETE', 'Complete', 4, 1, NOW());