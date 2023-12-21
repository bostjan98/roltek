<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vue.js Example</title>
  <!-- Vue.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
  <!-- Axios CDN (for making API calls) -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/moment@2"></script>
  <link type="text/css" rel="stylesheet" href="<?php echo $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>assets/css/app.css"></link>
</head>
<body>

<div id="app">
  <h1>My List Component</h1>
  <div style="float:right; margin-right:10%;">
    <label for="sortDirectionSelect">Sort Priority:</label>
    <select id="sortDirectionSelect" v-model="sortDirection">
      <option value="asc">Ascending (10 to 90)</option>
      <option value="desc">Descending (90 to 10)</option>
    </select>
  </div>
  <div style="margin-bottom:1%;">
    <label for="acDocumentNr">Filter by acDocumentNr:</label>
    <input type="text" id="acDocumentNr" v-model="filters.acDocumentNr">
    <button @click="clearDocumentNr">Clear</button>
  </div>
  <div>
    <label for="status">Filter by status:</label>
    <select id="status" v-model="filters.status">
      <option value="">All</option>
      <option value="1">New</option>
      <option value="2">In progress</option>
      <option value="3">In error</option>
      <option value="4">Done</option>
      <option value="5">Error in migration</option>
    </select>
  </div>
  <div>&nbsp;</div>
  <div>
    <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
    <span>Page {{ currentPage }} of {{ totalPages }}</span>
    <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
  </div>
  
  <table>
    <thead>
      <tr>
        <th>anId</th>
        <th>acOID</th>
        <th>Document No</th>
        <th>Status</th>
        <th>Priority</th>
        <th>Date Inserted</th>
        <th>Date Updated</th>
        <th>Task Type</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in paginatedItems" :key="item.id">
        <td>{{ item.anId }}</td>
        <td>{{ item.acOID }}</td>
        <td>{{ item.acDokumentenNr }}</td>
        <td>
          <span v-if="isLinkRequired(item.anIdStatus)">
            <a href="#" @click.prevent="openWindowWithText(item.anId)">
              {{ getStatusLabel(item.anIdStatus) }}
            </a>
          </span>
          <span v-else>
            {{ getStatusLabel(item.anIdStatus) }}
          </span>
        </td>
        
        <td>{{ item.anPriority }}</td>
        <td>{{ item.formattedDateInserted }}</td>
        <td>{{ item.formattedDateUpdated }}</td>
        <td>{{ item.anIdTaskType }}</td>
      </tr>
    </tbody>
  </table>
  <hr>
  <div>
    <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
    <span>Page {{ currentPage }} of {{ totalPages }}</span>
    <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
  </div>
  

  <!-- The modal -->
  <div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
      <p><b>Error Text from Status:</b</p>
      <p v-html="modalErrorText" style="font-size: 24px;"></p>
      <button @click="closeModal">Close</button>
    </div>
  </div>
</div>

<script>
  new Vue({
    el: '#app',
    data() {
      return {
        itemList: [], // Array to store API data
        filteredList: [], // Array to store filtered and sorted data
        filters: {
          acDocumentNr: '', // Filter by acDocumentNr
          status: '', // Filter by status
        },
        sortBy: 'anPriority', // Default sorting by priority
        sortDirection: 'asc', // Default sorting direction
        // Your existing data properties
        modalErrorText: '', // Additional property for modal error text
        currentPage: 1, // Current page number
        itemsPerPage: 20, // Number of items per page
      };
    },

    computed: {
        filteredAndSortedItems() {
          const filteredItems = this.itemList.filter(item => {
            // Apply filters
            const searchTerm = this.filters.acDocumentNr ? this.filters.acDocumentNr.toLowerCase() : null;
            const statusFilter = this.filters.status !== null && this.filters.status !== undefined
              ? this.filters.status.toString()
              : null;

            const itemAcDocumentNr = item.acDokumentenNr ? item.acDokumentenNr.toLowerCase() : null;

            const itemStatus = 'anIdStatus' in item && item.anIdStatus !== null && item.anIdStatus !== undefined
              ? item.anIdStatus.toString()
              : null;

            const matchSearchTerm = !searchTerm || (itemAcDocumentNr && itemAcDocumentNr.includes(searchTerm));
            const matchStatus = !statusFilter || (
              (statusFilter !== 'null' && statusFilter !== 'undefined') &&
              itemStatus !== undefined &&
              itemStatus !== null &&
              String(itemStatus) === statusFilter
            );

            return matchSearchTerm && matchStatus;
          });

          // Apply sorting
          filteredItems.sort((a, b) => {
              const aValue = this.sortBy === 'anPriority' ? parseInt(a.anPriority) : a[this.sortBy];
              const bValue = this.sortBy === 'anPriority' ? parseInt(b.anPriority) : b[this.sortBy];

              // Handle null values
              if (aValue === null || aValue === undefined) {
                return this.sortDirection === 'asc' ? -1 : 1;
              }
              if (bValue === null || bValue === undefined) {
                return this.sortDirection === 'asc' ? 1 : -1;
              }

              // Sort by priority
              if (this.sortBy === 'anPriority') {
                return this.sortDirection === 'asc' ? aValue - bValue : bValue - aValue;
              }

              // Default sorting logic for other properties
              return this.sortDirection === 'asc' ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
            });

            return filteredItems;
        },
        paginatedItems() {
          const startIndex = (this.currentPage - 1) * this.itemsPerPage;
          const endIndex = startIndex + this.itemsPerPage;
          return this.filteredAndSortedItems.slice(startIndex, endIndex);
        },
        totalItems() {
          return this.filteredAndSortedItems.length;
        },
        totalPages() {
          return Math.ceil(this.totalItems / this.itemsPerPage);
        },
    },
    
    mounted() {
      // Fetch data when the component is mounted
      this.fetchData();
    },
    methods: {
      async fetchData() {
        try {
          // Make API call using Axios
          const response = await axios.get('http://localhost/roltek/api/showData.php');
          this.itemList = response.data; // Update the itemList with API response
          this.itemList.forEach(item => {
            item.formattedDateInserted = moment(item.adDateInserted).format('DD.MM.YYYY HH:mm:ss');
            item.formattedDateUpdated = moment(item.adDateUpdated).format('DD.MM.YYYY HH:mm:ss');
        });
        } catch (error) {
          console.error('Error fetching data:', error);
        }
      },

      getStatusLabel(status) {
        const statusMap = {
          1: 'New',
          2: 'In Progress',
          3: 'In Error',
          4: 'Done',
          5: 'Error in Migration',
          // Add more mappings as needed
        };

        return statusMap[status] || status; // Return label or original value if not found
      },

      isLinkRequired(status) {
        if (status == 3 || status == 5){
          return true;
        }else{
          return false;
        }
      },

      openWindowWithText(id) {
        const apiUrl = `http://localhost/roltek/api/getErrorText.php?id=${id}`;

        axios.get(apiUrl)
          .then(response => {
            // Set the error text and show the modal
            this.modalErrorText = response.data.acError;
            this.openModal();
          })
          .catch(error => {
            console.error('Error fetching error text:', error);
          });
      },
      
      openModal() {
        // Show the modal
        document.getElementById("myModal").style.display = "block";
      },

      closeModal() {
        // Close the modal
        document.getElementById("myModal").style.display = "none";
      },
 

      // Function to handle sorting
      handleSort(column) {
        if (this.sortBy === column) {
          this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
          this.sortBy = column;
          this.sortDirection = 'asc';
        }
      }, 
      
      prevPage() {
        if (this.currentPage > 1) {
          this.currentPage -= 1;
        }
      },
      nextPage() {
        if (this.currentPage < this.totalPages) {
          this.currentPage += 1;
        }
      },
      clearDocumentNr() {
        this.filters.acDocumentNr = ''; // Clear the acDocumentNr filter
      },
    },
  });
  </script>
</body>
</html>
