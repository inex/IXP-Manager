 # Aggregators
 
 An *aggregator* is a class comprising functions that aggregate data from one or mode database tables in such a way that cannot be done cleanly or easily with SQL.
 
 The concept is similar to Doctrine's Repository classes but not a direct replacement:
 
 * functions that easily operator on multiple rows still belong in the model class;
 * scopes and similar filtering functions belong in the model class;
 * aggregation type functions that are ~ <10 lines long and are used once in a single controller are better placed in the controller action or a private function in the controller.
 
 So an aggregator is really a library of model functions that typically involve more than one database table and/or are complex and/or are used in more than one place in the code (e.g. multiple controllers with no parent/child relationship).
 
 