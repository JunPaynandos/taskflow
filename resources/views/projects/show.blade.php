    @extends('layouts.app')

    @if (session('success'))
        <div class="alert alert-dismissible fade show" id="successAlert" style="position: fixed; background: #22bf76; color: white; top: 20px; right: 20px; z-index: 1050; width: auto; max-width: 350px;">
            {{ session('success') }}
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    let successAlert = document.getElementById('successAlert');
                    if (successAlert) {
                        setTimeout(function () {
                            successAlert.classList.remove('show');
                            successAlert.classList.add('fade');
                        }, 5000);
                    }
                });
            </script>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-dismissible fade show" id="notExitsAlert" style="position: fixed; background: #ffd351; color: white; top: 20px; right: 20px; z-index: 1050; width: auto; max-width: 350px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    let notExitsAlert = document.getElementById('notExitsAlert');
                    if (notExitsAlert) {
                        setTimeout(function () {
                            notExitsAlert.classList.remove('show');
                            notExitsAlert.classList.add('fade');
                        }, 5000);
                    }
                });
            </script>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-dismissible fade show" id="errorAlert" style="position: fixed; background: #ffd351; color: white; top: 20px; right: 20px; z-index: 1050; width: auto; max-width: 350px;">
            {{ session('error') }}
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    let errorAlert = document.getElementById('errorAlert');
                    if (errorAlert) {
                        setTimeout(function () {
                            errorAlert.classList.remove('show');
                            errorAlert.classList.add('fade');
                        }, 5000);
                    }
                });
            </script>
        </div>
    @endif

    @section('content')
        <div class="container mt-5 mb-4">
            <h1 class="text-center mb-4" style="font-size: 2rem;">Project: {{ $project->name }}</h1>
            <div class="card p-4 mb-2" style="background: transparent; border: none; font-size: 1.1rem; position: relative; left: -20px;">
                <p><strong>Description:</strong> {{ $project->description }}</p>
                <!-- <p><strong>Created At:</strong> {{ $project->created_at->format('M d, Y') }}</p> -->
            </div>
            
                <div class="d-flex justify-content-start mb-4 gap-2">
                    <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                        Back to Projects
                    </a>
                    @if($isOwner)
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            Send Invitation
                        </button>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                            Add Task
                        </button>
                    @endif
                    <button id="openChatBtn" class="btn btn-primary" style="position: fixed; bottom: 20px; right: 50px; z-index: 9999;">Open Chat</button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#projectProgressModal">
                        View Project Progress
                    </button>
                </div>

            <!-- Member List -->
            <div class="mb-4">
                <h3 class="h4">Members:</h3>
                @if ($project->users->isEmpty() && $project->invitations->isEmpty())
                    <p>No members invited yet.</p>
                @else
                    <ul class="list-group">
                        @foreach ($project->users as $user)
                            <li class="list-group-item" style="background: #e7e8e9;">{{ $user->name }}</li>
                        @endforeach
                    </ul>

                    @if ($project->invitations->isNotEmpty())
                        <h5 class="mt-3">Pending Invitations:</h5>
                        <ul class="list-group">
                            @foreach ($project->invitations as $invitation)
                                @if ($invitation->status == 'pending')
                                    <li class="list-group-item" style="background: #fff3cd;">
                                        {{ $invitation->user->name }} (Pending)
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                @endif
            </div>

            <!-- Modal for Adding User -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Send Invitation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('projects.addUser', $project->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="user_email" class="form-label">Enter User Email:</label>
                                    <input type="email" name="user_email" id="user_email" class="form-control" placeholder="Enter email of user to invite" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Send Invitation</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for Adding Task -->
            <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addTaskModalLabel">Create Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('projects.createTask', $project->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">Task Name:</label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description:</label>
                                    <textarea name="description" id="description" class="form-control"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="assigned_to" class="form-label">Assign to:</label>
                                    <select name="assigned_to" id="assigned_to" class="form-select">
                                        @foreach ($project->users as $user) <!-- Only project members -->
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date:</label>
                                    <input type="date" name="due_date" id="due_date" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status:</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="not started">Not Started</option>
                                        <option value="in progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success">Create Task</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Box Modal -->
            <div id="chatBox" style="display: none; position: fixed; bottom: 50px; right: 20px; width: 300px; height: 400px; background: white; border: 1px solid #ddd; border-radius: 5px; z-index: 9999; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                <div style="background: #007bff; color: white; padding: 10px; text-align: center;">
                    <strong>Chat</strong>
                    <button id="closeChatBtn" style="background: none; border: none; color: white; font-size: 18px; cursor: pointer; position: absolute; right: 10px;">&times;</button>
                </div>
                <div id="chatMessages" style="padding: 10px; height: 300px; overflow-y: auto; background: #f9f9f9;"></div>
                <div style="padding: 10px; background: #f9f9f9;">
                    <input type="text" id="chatMessageInput" placeholder="Type a message..." style="width: 100%; padding: 5px;">
                    <button id="sendMessageBtn" class="btn btn-primary" style="width: 100%; margin-top: 5px;">Send</button>
                </div>
            </div>

            <!-- Modal for Project and Member Progress -->
<div class="modal fade" id="projectProgressModal" tabindex="-1" aria-labelledby="projectProgressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectProgressModalLabel">Project Progress and Members' Contribution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Project Overall Progress -->
                <div class="mb-4">
                    <h4>Overall Project Progress</h4>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                            {{ round($progress, 2) }}%
                        </div>
                    </div>
                </div>

                <!-- Members' Progress -->
                <div class="mb-4">
                    <h4>Members' Contribution</h4>
                    @foreach ($memberProgress as $userId => $member)
                        <div class="mb-3">
                            <h5>{{ $member['name'] }}</h5>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $member['progress'] }}%;" aria-valuenow="{{ $member['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ round($member['progress'], 2) }}%
                                </div>
                            </div>
                            <p><strong>Tasks:</strong></p>
                            <ul class="list-group">
                                @foreach ($member['tasks'] as $task)
                                    <li class="list-group-item">
                                        <strong>{{ $task->name }}</strong> - Status: {{ ucfirst($task->status) }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>


            <!-- Tasks List -->
            <div class="mt-4">
                <h3 class="h4">Tasks:</h3>
                @if ($project->tasks->isEmpty())
                    <p>No tasks assigned yet.</p>
                @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="10%" scope="col">Task Name</th>
                            <th width="16%" scope="col">Description</th>
                            <th width="12%" scope="col">Assigned To</th>
                            <th width="30%" scope="col">Status</th>
                            <th width="15%" scope="col">Due Date</th>
                            <th width="10%" scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($project->tasks as $task)
                            <tr>
                                <td>{{ $task->name }}</td>
                                <td>{{ $task->description }}</td>
                                <td>{{ $task->assignedUser ? $task->assignedUser->name : 'Not Assigned' }}</td>
                                <td>
                                    <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-2">
                                            <select name="status" class="form-select" id="selection" style="width: 330px;" required>
                                                <option value="not started" {{ $task->status == 'not started' ? 'selected' : '' }}>Not Started</option>
                                                <option value="in progress" {{ $task->status == 'in progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </div>

                                        <!-- Flex container for file input and button -->
                                        <div class="d-flex align-items-center mb-2">
                                            <input type="file" name="output_file" class="form-control custom-file-input" style="width: 230px;">
                                            <button type="submit" class="btn btn-success update-btn" style="width: 100px;">Update</button>
                                        </div>
                                    </form>
                                </td>

                                <td>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    @if($task->output_file_path)
                                        <a href="{{ route('tasks.downloadOutput', $task->id) }}" class="btn btn-primary view-output">
                                            Download
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <br><br><br><br><br>

        <style>
            .custom-file-input {
                background-color: #f8f9fa;
                border: 1.5px solid gray;
                border-radius: 5px;
                padding: 5px;
                font-size: 1rem;
                display: inline-block;
                height: 38px;
                box-sizing: border-box;
                border-top-right-radius: 0;
                border-bottom-right-radius: 0;
            }

            .custom-file-input:focus {
                border-color: #28a745;
                outline: none;
                box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
            }

            #selection {
                border: 1.5px solid gray;
            }

            .form-select,
            .custom-file-input,
            .btn {
                height: 38px;
            }

            .update-btn {
                border-top-left-radius: 0;
                border-bottom-left-radius: 0;
            }
        </style>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.13.1/dist/echo.iife.js"></script>
        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <script>
            $(document).ready(function () {
                const openChatBtn = $('#openChatBtn');
                const closeChatBtn = $('#closeChatBtn');
                const chatBox = $('#chatBox');
                const chatMessages = $('#chatMessages');
                const chatMessageInput = $('#chatMessageInput');
                const sendMessageBtn = $('#sendMessageBtn');
                const projectId = {{ $project->id }}; // Dynamic project ID

                // Function to fetch chat messages when the chat box is opened
                function fetchChatMessages() {
                    axios.get(`/get-chat-messages/${projectId}`)
                        .then(function (response) {
                            chatMessages.empty(); // Clear previous messages

                            response.data.forEach(message => {
                                chatMessages.append(`
                                    <div>
                                        <strong>${message.user_name}</strong> <small>(${message.created_at})</small>
                                        <p>${message.message}</p>
                                    </div>
                                `);
                            });
                            chatMessages.scrollTop(chatMessages[0].scrollHeight); // Scroll to the bottom
                        })
                        .catch(function (error) {
                            console.error('Error fetching messages:', error);
                        });
                }

                // Open the chat and fetch messages when the button is clicked
                if (openChatBtn.length) {
                    openChatBtn.on('click', function () {
                        chatBox.show(); // Show the chat box
                        fetchChatMessages(); // Fetch the messages when opening the chat box
                    });
                }

                // Close the chat box
                if (closeChatBtn.length) {
                    closeChatBtn.on('click', function () {
                        chatBox.hide(); // Hide the chat box
                    });
                }

                // Send a message when the send button is clicked
                if (sendMessageBtn.length) {
                    sendMessageBtn.on('click', function () {
                        const message = chatMessageInput.val();

                        if (message) {
                            // Emit the message to the server
                            axios.post(`/send-chat-message/${projectId}`, { message: message })
                                .then(function (response) {
                                    chatMessageInput.val(''); // Clear the input field

                                    console.log('Message sent response:', response.data);

                                    // Manually append the message to the chat (for immediate feedback)
                                    chatMessages.append(`
                                        <div>
                                            <strong>${response.data.data.user_name}</strong> 
                                            <small>(${response.data.data.created_at})</small>
                                            <p>${response.data.data.message}</p>
                                        </div>
                                    `);
                                    chatMessages.scrollTop(chatMessages[0].scrollHeight); // Scroll to the bottom
                                })
                                .catch(function (error) {
                                    console.error('Error sending message:', error);
                                });
                        }
                    });
                }

                // Initialize Echo to listen for the MessageSent event
                if (typeof Echo !== 'undefined') {
                    console.log("Echo is defined!");
                    Echo.channel('project.' + projectId)
                        .listen('MessageSent', function (event) {
                            console.log('Received event:', event);

                            console.log('User Name:', event.user_name);
                            console.log('Message:', event.message);
                            console.log('Created At:', event.created_at);
                            
                            // Append the broadcasted message to the chat
                            chatMessages.append(`
                                <div>
                                    <strong>${event.message.user_name}</strong> 
                <small>(${event.message.created_at})</small>
                <p>${event.message.message}</p>
                                </div>
                            `);
                            chatMessages[0].offsetHeight;
                            chatMessages.scrollTop(chatMessages[0].scrollHeight); // Scroll to the bottom
                        });
                } else {
                    console.error('Echo is not defined.');
                }
            });
        </script>
        <script>
            document.getElementById('openModalBtn').addEventListener('click', function () {
                var myModal = new bootstrap.Modal(document.getElementById('addUserModal'));
                myModal.show();
            });
        </script>
    @endsection
