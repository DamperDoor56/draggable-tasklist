import React from "react";
import {getTasks} from "../utils";

// Renders a dropdown menu allowing the user to select a project
function SelectProject({projectId, projects, setProjectId, setTasks}) {
    // When a project is selected, it updates the state with the selected project ID
    const selectProject = (e) => {
        const value = e.target.value;
        setProjectId(value);
        if (value === '') {
            setTasks([]);
        } else {
            // fetches tasks for that project using getTasks, and updates the state with the retrieved tasks
            getTasks(value).then((tasksData) => setTasks(tasksData));
        }
    };

    return (
        <div className="projects">
            <select className="projects-select"
                    value={projectId}
                    onChange={selectProject}>
                <option value="">Choose a project</option>
                {projects.map((project) => (
                    <option key={project.id}
                            value={project.id}>{project.name}</option>
                ))}
            </select>
        </div>
    );
}

export default SelectProject;