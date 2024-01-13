# 

CODE IS WIP - DO NOT USE

## Data Type Reference

---

### Scalar Types (0-Dimensional)

#### `NA`
- **Description**: Represents a missing or unknown value.
- **Example**: `NA`

#### `Null`
- **Description**: Signifies an intentionally absent or unassigned value.
- **Example**: `Null`

#### `NaN`
- **Description**: Represents the result of undefined mathematical operations (e.g., 0/0).
- **Example**: `NaN`

#### `Boolean`
- **Description**: A true or false value.
- **Example**: `true`, `false`

#### `Byte`
- **Description**: Represents a single byte of data (0 to 255).
- **Example**: `0x5A`

#### `Numeric`
- **Description**: Includes integers, floating-point, or complex numbers
- **Example**: `42`, `3.14`

#### `String`
- **Description**: Represents a sequence of 1 or more characters.
- **Example**: `a`, `"Hello, World!"`

#### `Date`
- **Description**: A `String` or `Numeric` That is Detected as being a date
- **Example**: `1705128571`, `2024-01-12`, `2024-01-12T14:23:43Z`

#### `Location`
- **Description**: A `String` or `Numeric` That is Detected as being a Location
- **Example**: `United States of America`, `USA`, `us`
- **Inference**: Usually only able to be inferred from context when utilized in higher dimensional structure type.


---

## List Types (1-Dimensional)

#### `Array`
- **Description**: A generic list of values
- **Example**: `[1, "apple", true]`

#### `Vector`
- **Description**: An `Array` of identical Types
- **Example**: `[1.1, 2.2, 3.3]`

#### `NumericVector`
- **Description**: An `Vector` of `Numeric` Types
- **Example**: `[1.1, 1.1, 2.2, 3.3]`

#### `ByteVector`
- **Description**: A `Vector` of `Byte` Types
- **Example**: `[0x5A, 0x2D, 0x6F]`

#### `DateVector`
- **Description**: A `Vector` of `Date` Types
- **Example**: `[2023-01-01T00:00:00,2023-01-01T00:00:00,2023-01-01T00:00:01]`
- **Usage**: Found in transactional record columns

#### `LocationVector`
- **Description**: A `Vector` of `Country` Types
- **Example**: `[US, CA, US, FR, US]`
- **Usage**: Found in transactional record columns

#### `Series`
- **Description**: An ordered or sequential `Vector` 
- **Example**: `[0, 1, 1, 2, 2, 3, 4]`
- **Inference**: Sequential nature can only be inferred from context when utilized in higher dimensional structure type.

#### `DateSeries`
- **Description**: A `Series` of `Date` Types
- **Example**: `["2024-01-12","2024-01-12", "2024-01-13", "2024-01-14"]`
- **Inference**: Inferred when date ar
 
#### `Set`
- **Description**: A `Vector` of unique Values
- **Example**: `[3, 1, 4, 2]`

#### `CategorySet`
- **Description**: A `Set` of `String` Values
- **Example**: `["apple", "banana", "cherry"]`

#### `DateSet`
- **Description**: A `Set` of `Date` Values
- **Example**: `[2024-01-12, 2024-01-13, 2024-01-14]`

#### `CountrySet`
- **Description**: A `Set` of `Country` Values
- **Example**: `[USA, FR, CA]`

#### `SeriesSet`
- **Description**: Matches criteria of both `Series` and `Set`
- **Example**: `[0, 1, 2, 3, 4]`
- **Inference**: Usually only able to be inferred from context when utilized in higher dimensional structure type.

#### `DateSeriesSet`
- **Description**: Matches criteria of both `DateSeries` and `Set` (made of dates, order matters, all unique)
- **Example**: `["2024-01-12", "2024-01-13", "2024-01-14"]`
- **Inference**: Inferred when date ar

---

### Table Types (2-Dimensional)

#### `Frame`
- **Description**: An `Array` of 2 or more `Array` items equal in length
- **Note**: In Frames,nested arrays are called `Column` Items
- **Example**: `[["Name", "Age"], ["Alice", 30], ["Bob", 25]]`
- **Superset of**: `Table`, `Dictionary`, `Matrix`, `DataFrame`, `CategoryValues`, `DateValues`

#### `Dictionary`
- **Description**: A `Frame` of 2 Columns: 1 of type `Set` and the other of type `Array`
- **Example**: `{"Names": ["Alice", "Bob"], "Ages": [30, 25]}`

#### `DataFrame`
- **Description**: A `Frame` of 2 or more `Vector` Columns
- **Example**: `[["Alice", "Bob"], [30, 25], [true, false]]`

#### `CategoryNumericFrame`
- **Description**: A `DataFrame` with exactly 1 `CategorySet` and 1 or more `NumericVector` Columns
- **Example**: `[["Alice", "Bob"], [30, 25], [100, 109]]`

#### `DateNumericFrame`
- **Description**: A `DataFrame` with exactly 1 `DateSet` or `DateSeriesSet` and 1 or more `NumericVector` Columns
- **Example**: `[[2024-01-12, 2024-01-13], [30, 25], [100, 109]]`

#### `Matrix`
- **Description**: A `Frame` of matching `Vector` Types
- **Example**: `[[1, 2, 3], [4, 5, 6], [7, 8, 9]]`

---

### Other Types

#### Function
- **Description**: Represents a callable function.
- **Example**: A function definition or lambda expression.

#### Object
- **Description**: A generic object type for more complex structures.
- **Example**: An instance of a user-defined class.

---

