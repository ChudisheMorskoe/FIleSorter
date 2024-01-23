# File Sorter

Hi there! 👋 Thank you for checking out the File Sorter project.

File Sorter is a tool designed to organize your Downloads folder by sorting files based on their formats and archiving files that haven't been modified for more than 6 months. The script is configured to run each time your computer starts up.

## How to Run

To use File Sorter, make sure you have an Ubuntu/Linux operating system installed.

1. Open a terminal.
2. Navigate to the directory where the project is stored using the `cd` command.
3. Run the command: `./install`
   - This command installs the necessary dependencies.

4. Add the script to your computer's autoloader by executing the following commands:
   ```bash
   nano ~/.bashrc
   ```
   In the opened window, add the following line:
   ```bash
  php -f "/full/path/to/your/FileSorter.php"
   ```
   Save and close the file.

5. Update the changes by running:
   ```bash
   source ~/.bashrc
   ```

Now, File Sorter is set up to automatically organize your Downloads folder. Feel free to reach out if you have any questions or need further assistance! 🚀
