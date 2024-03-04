# `data/utils.php` Documentation

This file contains utility functions for data manipulation and retrieval in a PHP project. Here are the functions defined in this file:

## `dataCreateRow` Function

This function is used to create a new row in the data.

### Parameters

- `$data_parent_id` (required): The ID of the parent data.
- `$data_key` (required): The key of the data.
- `$data_type` (required): The type of the data.

### Return Value

The function returns the ID of the newly created data row.

## `dataNew` Function

This function is used to create a new data or retrieve an existing one.

### Parameters

- `$path` (required): The path where the data is located.
- `$create` (optional): A boolean indicating whether to create a new data if it does not exist. The default value is `false`.

### Return Value

The function returns the ID of the data.

## `dataSet` Function

This function is used to set a value to a data.

### Parameters

- `$path_array` (required): An array representing the path of the data.
- `$value` (required): The value to set.
- `$addHistory` (optional): A boolean indicating whether to add this operation to the history. The default value is `true`.

### Return Value

The function does not return a value.

## `dataGet` Function

This function is used to get the value of a data.

### Parameters

- `$path` (required): An array representing the path of the data.

### Return Value

The function returns the value of the data.

## `dataDelete` Function

This function is used to delete a data.

### Parameters

- `$path` (required): An array representing the path of the data.

### Return Value

The function returns a boolean indicating whether the deletion was successful.

## `dataExist` Function

This function is used to check whether a data exists.

### Parameters

- `$path` (required): The path where the data is located.

### Return Value

The function returns a boolean indicating whether the data exists.

## `dataKeys` Function

This function is used to get the keys of a data.

### Parameters

- `$path` (required): An array representing the path of the data.
- `$page` (optional): The page number for the results. The default value is `1`.
- `$size` (optional): The number of results per page. The default value is `PAGE_SIZE_DEFAULT`.

### Return Value

The function returns a list of keys.

## `dataCount` Function

This function is used to count the number of data in a path.

### Parameters

- `$path` (required): An array representing the path of the data.

### Return Value

The function returns the number of data.

## `dataInfo` Function

This function is used to get the information of a data.

### Parameters

- `$path` (required): An array representing the path of the data.

### Return Value

The function returns an array containing the information of the data.

## `dataHistory` Function

This function is used to get the history of a data.

### Parameters

- `$path_array` (required): An array representing the path of the data.
- `$page` (optional): The page number for the results. The default value is `1`.
- `$size` (optional): The number of results per page. The default value is `PAGE_SIZE_DEFAULT`.

### Return Value

The function returns a list of history records.

## `dataInc` Function

This function is used to increment the value of a data.

### Parameters

- `$path` (required): An array representing the path of the data.
- `$inc_val` (required): The increment value.
- `$addHistory` (optional): A boolean indicating whether to add this operation to the history.

### Return Value

The function returns the new value of the data.

## `dataDec` Function

This function is used to decrement the value of a data.

### Parameters

- `$path` (required): An array representing the path of the data.
- `$dec_val` (required): The decrement value.
- `$addHistory` (optional): A boolean indicating whether to add this operation to the history.

### Return Value

The function returns the new value of the data.

## `dataSearch` Function

This function is used to search for specific data.

### Parameters

- `$path` (required): The path where the data is located.
- `$search_text` (required): The text to search for.
- `$page` (optional): The page number for the results. The default value is `1`.
- `$size` (optional): The number of results per page. The default value is `PAGE_SIZE_DEFAULT`.

### Return Value

The function returns a list of data keys that match the search text.

## `dataCommit` Function

This function is used to commit the changes made to the data.

### Parameters

This function does not accept any parameters.

### Return Value

This function does not return a value.