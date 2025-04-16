<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/session.php';

if (!isTeacherLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

$message = '';
$error = '';
$teacher_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $test_name = $_POST['test_name'] ?? '';
    $question_ids = isset($_POST['question_ids']) ? explode(',', $_POST['question_ids']) : [];

    if ($test_name && count($question_ids)) {
        $stmt = $pdo->prepare("INSERT INTO tests.tests (test_name, created_by) VALUES (:name, :created_by) RETURNING id");
        $stmt->execute([
            'name' => $test_name,
            'created_by' => $teacher_id
        ]);
        $test_id = $stmt->fetchColumn();

        $stmt = $pdo->prepare("INSERT INTO tests.test_questions (test_id, question_id) VALUES (:tid, :qid)");
        foreach ($question_ids as $qid) {
            $stmt->execute(['tid' => $test_id, 'qid' => $qid]);
        }

        $message = "Тест «$test_name» успешно создан.";
    } else {
        $error = "Введите название теста и выберите хотя бы один вопрос.";
    }
}

$stmt = $pdo->query("SELECT id, question_text, question_type FROM tests.questions ORDER BY id");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Создание теста</title>
  <link rel="stylesheet" href="/style.css">
  <style>
    body { font-family: sans-serif; display: flex; flex-direction: column; gap: 20px; padding: 20px; }
    .container { display: flex; gap: 20px; }
    .column { flex: 1; border: 1px solid #ccc; border-radius: 8px; padding: 10px; min-height: 400px; }
    .question { border: 1px solid #aaa; padding: 8px; margin-bottom: 6px; border-radius: 5px; background: #f9f9f9; cursor: grab; }
    .question.dragging { opacity: 0.5; }
    .filters { display: flex; gap: 10px; margin-bottom: 10px; }
    button { padding: 5px 10px; }
    input[type="text"] { padding: 5px; width: 200px; }
  </style>
</head>
<body>
  <h2>Создание нового теста</h2>

  <?php if (!empty($message)): ?>
    <p style="color:green"><?= htmlspecialchars($message) ?></p>
  <?php elseif (!empty($error)): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" onsubmit="return collectSelectedQuestions()">
    <label>Название теста:<br>
      <input type="text" name="test_name" required>
    </label><br><br>

    <div class="filters">
      <label>Тип: 
        <select id="filterType">
          <option value="">Все</option>
          <option value="single">Один ответ</option>
          <option value="multiple">Несколько</option>
          <option value="text">Текст</option>
        </select>
      </label>
      <input type="text" id="searchInput" placeholder="Поиск по вопросу">
      <button type="button" id="selectAll">Выбрать все</button>
      <button type="button" id="clearSelection">Сбросить выбранные</button>
    </div>

    <div class="container">
      <div class="column" id="allQuestions">
        <h3>Все вопросы</h3>
      </div>

      <div class="column" id="selectedQuestions">
        <h3>Выбранные вопросы</h3>
      </div>
    </div>

    <input type="hidden" name="question_ids" id="questionIds">
    <br>
    <button type="submit">Создать тест</button>
  </form>

  <p><a href="dashboard.php">← Назад в панель преподавателя</a></p>

  <script>
    const questions = <?= json_encode($questions) ?>;

    const allQuestionsDiv = document.getElementById('allQuestions');
    const selectedQuestionsDiv = document.getElementById('selectedQuestions');
    const filterType = document.getElementById('filterType');
    const searchInput = document.getElementById('searchInput');

    function createQuestionElement(question) {
      const div = document.createElement('div');
      div.className = 'question';
      div.draggable = true;
      div.dataset.id = question.id;
      div.dataset.type = question.question_type;
      div.textContent = question.question_text;

      div.addEventListener('dragstart', e => {
        div.classList.add('dragging');
        e.dataTransfer.setData('text/plain', JSON.stringify(question));
      });

      div.addEventListener('dragend', () => {
        div.classList.remove('dragging');
      });

      div.addEventListener('dblclick', () => {
        if (div.parentElement.id === 'allQuestions') {
          selectedQuestionsDiv.appendChild(div);
        } else {
          allQuestionsDiv.appendChild(div);
        }
      });

      return div;
    }

    function renderQuestions() {
      allQuestionsDiv.innerHTML = '<h3>Все вопросы</h3>';
      const type = filterType.value;
      const keyword = searchInput.value.toLowerCase();

      for (const q of questions) {
        if ((type && q.question_type !== type) || (keyword && !q.question_text.toLowerCase().includes(keyword))) continue;
        if (!selectedQuestionsDiv.querySelector(`[data-id='${q.id}']`)) {
          allQuestionsDiv.appendChild(createQuestionElement(q));
        }
      }
    }

    allQuestionsDiv.addEventListener('dragover', e => e.preventDefault());
    selectedQuestionsDiv.addEventListener('dragover', e => e.preventDefault());

    allQuestionsDiv.addEventListener('drop', e => {
      const q = JSON.parse(e.dataTransfer.getData('text/plain'));
      if (!allQuestionsDiv.querySelector(`[data-id='${q.id}']`)) {
        const el = createQuestionElement(q);
        allQuestionsDiv.appendChild(el);
      }
    });

    selectedQuestionsDiv.addEventListener('drop', e => {
      const q = JSON.parse(e.dataTransfer.getData('text/plain'));
      if (!selectedQuestionsDiv.querySelector(`[data-id='${q.id}']`)) {
        const el = createQuestionElement(q);
        selectedQuestionsDiv.appendChild(el);
      }
    });

    filterType.addEventListener('change', renderQuestions);
    searchInput.addEventListener('input', renderQuestions);

    document.getElementById('selectAll').addEventListener('click', () => {
      const all = allQuestionsDiv.querySelectorAll('.question');
      all.forEach(q => selectedQuestionsDiv.appendChild(q));
    });

    document.getElementById('clearSelection').addEventListener('click', () => {
      const all = selectedQuestionsDiv.querySelectorAll('.question');
      all.forEach(q => allQuestionsDiv.appendChild(q));
    });

    function collectSelectedQuestions() {
      const ids = Array.from(selectedQuestionsDiv.querySelectorAll('.question')).map(q => q.dataset.id);
      document.getElementById('questionIds').value = ids.join(',');
      return true;
    }

    renderQuestions();
  </script>
</body>
</html>
