# File Sorter

👋 Hi there! Thanks for exploring the File Sorter project.

File Sorter is a handy utility designed to bring order to your Downloads folder by organizing files based on their formats. Additionally, it intelligently archives files that have remained untouched for more than 6 months. The script seamlessly runs each time your computer boots up.

## How to Run

To get File Sorter up and running, ensure you have an Ubuntu/Linux operating system installed.

1. Open a command prompt.
2. Navigate to the directory where the project is stored using the `cd` command.
3. Execute the following command: `./install`.
   - This command automagically installs all the required dependencies.

4. Now, let's add the script to your computer's autoloader by running the following commands:
   ```bash
   nano ~/.bashrc
   ```
   In the opened window, append the following line:
   ```bash
   php -f "/full/path/to/your/FileSorter.php"
   ```
   Save and close the file.

5. Update the changes with the following command:
   ```bash
   source ~/.bashrc
   ```

Voilà! Your File Sorter is now set to gracefully handle the organization of your Downloads folder. Feel free to reach out if you have any questions or need assistance! 🚀
