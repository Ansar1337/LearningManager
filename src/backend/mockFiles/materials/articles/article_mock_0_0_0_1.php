<?php
ob_start();
?>

<h1>Kotlin compilation</h1>

<p>This tutorial demonstrates how to use IntelliJ IDEA for creating a console application. To get started, first download and install the <a href="https://www.jetbrains.com/ru-ru/idea/download">latest version of IntelliJ IDEA.</a></p>
<br />
<p>Before you start working with Kotlin, make sure that the plugin is enabled. The plugin is bundled with IntelliJ IDEA and is activated by default. If the plugin is not activated, enable it on the Plugins page of the IDE settings</p>
<br />
<p>
IntelliJ IDEA provides first-class support for Kotlin. It includes Kotlin-specific project templates, coding assistance, refactorings, debugging, analysis tools, and so on. Most of the Java tools are available for Kotlin, so, for example, if you know how to use Java debugger or refactorings, your experience will be enough to use these in Kotlin. In this topic, you will find the instructions to get started with Kotlin in IntelliJ IDEA.
</p>

<h2>Create a Kotlin project</h2>

<ol>
    <li>On the Welcome screen, click New Project. Alternatively, in the main menu, go to File | New | Project.</li>
    <li>From the list on the left, select New Project.</li>
    <li>Name the new project and change its location if necessary.</li>
    <li>Select the Create Git repository checkbox to place the new project under version control.</li>
    <li>From the Language list, select Kotlin.</li>
    <li>Select the IntelliJ build system. It's a native builder that doesn't require downloading additional artifacts.</li>
    <li>From the JDK list, select the JDK that you want to use in your project.If the JDK is installed on your computer, but not defined in the IDE, select Add JDK and specify the path to the JDK home directory. If you don't have the necessary JDK on your computer, select Download JDK.</li>
    <li>Enable the Add sample code option to create a file with a sample Hello World! application.</li>
    <li>Click Create.</li>
</ol>

<?php
return ob_get_clean();
?>