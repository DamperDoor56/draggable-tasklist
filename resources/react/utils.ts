import axiosConfig from './axiosConfig';
import { toast } from 'react-toastify';

// Returns the error message if the input is an instance of Error and converts it to a string
export const getErrorMessage = (error: unknown) => {
    if (error instanceof Error) return error.message;
    return String(error)
}
// Retrieves tasks for a given project ID
export const getTasks = async (projectId) => {
    if (!projectId) {
        toast.error("Project is required!");
        return;
    }

    try {
        const response = await axiosConfig.get(`/tasks?project_id=${projectId}`);
        const { success, tasks, message } = response.data;

        if (success) {
            return tasks;
        } else {
            toast.error(message);
            return [];
        }
    } catch (err) {
        // Displays an error toast if the project ID is missing or if the API request is unsuccessful
        toast.error(getErrorMessage(err));
        return [];
    }
}
// Reorder tasks within a project based on start and end positions
export const reorderTasks = async (projectId: number, start: number, end: number) => {
    try {
        const response = await axiosConfig.put('/tasks', {
            project_id: projectId,
            start,
            end,
        });
        const { success, message } = response.data;
        // Displays a success or error toast based on the API response.
        toast[success ? 'success' : 'error'](message);
    } catch (err) {
        toast.error(getErrorMessage(err));
    }
}
// Update task information
export const editTask = async (task) => {
    // Validates that the task has an ID and a title before making the request
    if (!task.id) return;
    if (!task.title) {
        toast.error("Title is required!");
        return;
    }

    try {
        const response = await axiosConfig.put(`/tasks/${task.id}`, {
            title: task.title,
            description: task.description,
        });
        const { success, message } = response.data;

        toast[success ? 'success' : 'error'](message);
    } catch (err) {
        toast.error(getErrorMessage(err));
    }
}
// Delete a task by its ID
export const deleteTask = async (id) => {
    if (!id) {
        toast.error("Invalid task!");
        return;
    }

    try {
        const response = await axiosConfig.delete(`/tasks/${id}`);
        const { success, message } = response.data;

        toast[success ? 'success' : 'error'](message);
    } catch (err) {
        toast.error(getErrorMessage(err));
    }
}
// Create a new task for a given project ID
export const createTask = async (task, projectId) => {
    // Validates that the project ID exists, and the task has a title before making the request
    if (!projectId) {
        toast.error("Project is required!");
        return;
    }
    if (!task.title) {
        toast.error("Title is required!");
        return;
    }

    try {
        const response = await axiosConfig.post(`/tasks?project_id=${projectId}`, {
            title: task.title,
            description: task.description,
        });
        const { success, message } = response.data;

        toast[success ? 'success' : 'error'](message);
    } catch (err) {
        toast.error(getErrorMessage(err));
    }
}