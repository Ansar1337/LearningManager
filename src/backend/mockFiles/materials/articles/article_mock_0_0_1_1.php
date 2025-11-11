<?php
ob_start();
?>

<h1>Numbers type</h1>

<p>
In Kotlin, everything is an object in the sense that you can call member functions and properties on any variable. While certain types have an optimized internal representation as primitive values at runtime (such as numbers, characters, booleans and others), they appear and behave like regular classes to you.
</p>
<br />
<h3>Integer types</h3>
<p>
Kotlin provides a set of built-in types that represent numbers. For integer numbers, there are four types with different sizes and, hence, value ranges.
</p>
<br />
<img style="width: 100%; height: 100%; max-width: 800px;" src="/src/assets/images/materials/integer-kotlin-types-80322.png" alt="kotlin logo" />
<br />
<p>
When you initialize a variable with no explicit type specification, the compiler automatically infers the type with the smallest range enough to represent the value starting from Int. If it is not exceeding the range of Int, the type is Int. If it exceeds, the type is Long. To specify the Long value explicitly, append the suffix L to the value. Explicit type specification triggers the compiler to check the value not to exceed the range of the specified type.
</p>
<br />
<p>
val one = 1 // Int <br />
val threeBillion = 3000000000 // Long <br />
val oneLong = 1L // Long <br />
val oneByte: Byte = 1
</p>
<br />
<h3>Floating-point types</h3>
<br />
<p>
For real numbers, Kotlin provides floating-point types Float and Double that adhere to the IEEE 754 standard. Float reflects the IEEE 754 single precision, while Double reflects double precision.
</p>
<br />
<p>
These types differ in their size and provide storage for floating-point numbers with different precision:
</p>
<br />
<img style="width: 100%; height: 100%; max-width: 800px;" src="/src/assets/images/materials/float-kotlin-types-80323.png" alt="kotlin logo" />
<br />
<p>
You can initialize Double and Float variables with numbers having a fractional part. It's separated from the integer part by a period (.) For variables initialized with fractional numbers, the compiler infers the Double type:
</p>
<br />
<p>
val pi = 3.14 // Double <br />
// val one: Double = 1 // Error: type mismatch <br />
val oneDouble = 1.0 // Double
</p>
<br />
<?php
return ob_get_clean();
?>